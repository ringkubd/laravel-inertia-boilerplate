<template>
    <Head>
        <title>Support Management | IsDB-BISEW</title>
    </Head>
    <Authenticated>
        <div class="container">
            <div class="row h-screen">
                <div class="col-md-4">
                    <div class="card mt-1 shadow-sm" style="height: 85vh!important;">
                        <div class="card-header bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                            <h4 class="mb-2">Active Request List</h4>
                            <div class="input-group mb-2 mt-2">
                                <input type="text" class="form-control" v-model="searchTerm" placeholder="Search conversations..." aria-label="Search conversations">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body chat overflow-auto" style="height: 58vh!important; scrollbar-width: thin;">
                            <ChatUserList
                                :support="filteredSupport"
                                :active-conversation="activeConversation"
                                :online-user="onlineUser"
                                :unread-count="supportIdList"
                                :typing="allTyping"
                                @chat-selected="changeActiveChat"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-md-8" @drop.prevent="onImageDrop" @dragover.prevent>
                    <div class="card shadow-sm" style="height: 85vh!important;">
                        <div class="card-header bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                            <div class="flex items-center">
                                <h2>{{ activeConversation?.creator_name || 'Select a conversation' }}</h2>
                                <div v-if="isUserOnline(activeConversation?.creator)" class="user-online-indicator ml-2"></div>
                            </div>
                            <div v-if="activeConversation?.issues" class="text-sm text-gray-100">
                                Issue: {{ activeConversation.issues }}
                            </div>
                        </div>
                        <div class="card-body overflow-auto p-4 bg-gray-50" ref="messageContainer" style="height: 58vh!important; scrollbar-width: thin;">
                            <ChatMessages
                                :messages="messageData"
                                :conversation-creator-id="activeConversation?.creator"
                                @message-seen="seenMessage"
                                @context-menu="handleContextMenu"
                            />
                        </div>
                        <div v-if="typing && typingText" class="typing-indicator px-3 py-2 bg-gray-100">
                            <span class="font-medium">{{ typingUser.name }}</span> is typing<span class="dot-animation">...</span>
                        </div>
                        <MessageInput
                            :typing="typing"
                            :typing-user="typingUser"
                            :typing-text="typingText"
                            @message-submit="sendMessage"
                            @typing="handleTyping"
                            @like-clicked="likeButton"
                            @done-clicked="doneConversation"
                            @file-upload="uploadFile"
                        />
                    </div>
                </div>
            </div>
        </div>
        <CmpContextMenu :display="showContextMenu" ref="menu">
            <template #default>
                <div class="block">
                    <div class="bg-white w-36 border border-gray-300 rounded-lg shadow-lg flex flex-col text-sm text-gray-700">
                        <div class="flex hover:bg-red-100 my-1 py-2 px-2 rounded cursor-pointer transition-colors duration-200">
                            <div @click="deleteMessage" class="flex items-center w-full">
                                <font-awesome-icon icon="trash" size="md" class="text-red-500 mr-2"/>
                                Delete
                            </div>
                        </div>
                        <div class="flex hover:bg-blue-100 my-1 py-2 px-2 rounded cursor-pointer transition-colors duration-200">
                            <div class="flex items-center w-full" @click="copyMessage">
                                <font-awesome-icon icon="copy" size="md" class="text-blue-500 mr-2"/>
                                Copy
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </CmpContextMenu>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CmpContextMenu from "@/Components/Context-menu";
import ChatUserList from "@/Components/Support/ChatUserList";
import ChatMessages from "@/Components/Support/ChatMessages";
import MessageInput from "@/Components/Support/MessageInput";
import { useConfirm } from 'v3confirm';
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faPen, faTrash, faCopy, faPaperclip,
    faThumbsUp, faCheck, faDotCircle, faSearch
} from "@fortawesome/free-solid-svg-icons";

library.add(faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck, faDotCircle, faSearch);

export default {
    props: ['support', 'activeConversation'],
    components: {
        Authenticated,
        CmpContextMenu,
        ChatUserList,
        ChatMessages,
        MessageInput
    },
    data() {
        return {
            messageData: [],
            typing: false,
            typingUser: "",
            typingText: "",
            showContextMenu: false,
            clickedMessageId: null,
            allUserConv: [],
            supportIdList: [],
            allTyping: [],
            onlineUser: [],
            searchTerm: "",
            typingTimeout: null,
            typingThrottleTimeout: null
        }
    },
    mounted() {
        this.confirm = useConfirm();

        // Initialize messages if available
        if (this.activeConversation?.message) {
            this.messageData = this.activeConversation.message;
            // Scroll to bottom once messages are loaded
            this.scrollToBottom();
        }

        // Setup WebSocket listeners for real-time updates
        this.setupWebSocketListeners();

        // Set up channels for all support conversations
        if (this.support && Array.isArray(this.support)) {
            this.support.forEach(conversation => {
                this.setupConversationChannel(conversation);
            });
        }

        // Join the presence channel to receive online user updates
        window.Echo.join('support')
            .here(users => {
                this.onlineUser = users;
            })
            .joining(user => {
                if (!this.onlineUser.some(u => u.id === user.id)) {
                    this.onlineUser.push(user);
                }
            })
            .leaving(user => {
                this.onlineUser = this.onlineUser.filter(u => u.id !== user.id);
            });
    },
    methods: {
        isUserOnline(userId) {
            return this.onlineUser.some(user => user.id === userId);
        },
        setupWebSocketListeners() {
            // Clean up any previous listeners
            if (this.activeConversation?.id) {
                // First leave existing channel if any
                window.Echo.leave(`support.${this.activeConversation.id}`);

                // Now establish a new connection
                this.channel = window.Echo.private(`support.${this.activeConversation.id}`);

                // Listen for events on this new channel
                this.channel.listen('.SupportEvent', (e) => {
                    console.log('Support event received', e);

                    // Don't add our own messages twice (they're added in sendMessage)
                    if (e.conversation.sender.id !== this.$page.props.user.id) {
                        // Add the new message to the conversation
                        this.messageData.push(e.conversation);
                        this.scrollToBottom();

                        // Clear typing indicator when a message is received
                        if (this.typing && this.typingUser?.id === e.conversation.sender.id) {
                            this.typing = false;
                            this.typingText = "";
                            this.allTyping[e.conversation.sender.id] = undefined;
                        }
                    }
                });

                // Listen for typing events
                this.channel.listenForWhisper('typing', (e) => {
                    console.log('Typing whisper received', e);
                    this.handleTypingWhisper(e);
                });
            }

            // Join the presence channel for online user status
            window.Echo.join('support')
                .here(users => {
                    this.onlineUser = users;
                })
                .joining(user => {
                    if (!this.onlineUser.some(u => u.id === user.id)) {
                        this.onlineUser.push(user);
                        console.log('User joined:', user.name);
                    }
                })
                .leaving(user => {
                    this.onlineUser = this.onlineUser.filter(u => u.id !== user.id);
                    console.log('User left:', user.name);
                });
        },
        setupConversationChannel(conversation) {
            if (!conversation || !conversation.id) return;

            // Initialize unread count for this conversation
            this.supportIdList[conversation.id] = 0;

            // Listen for new messages on this conversation
            window.Echo.private(`support.${conversation.id}`)
                .listen('.SupportEvent', (e) => {
                    // Add to all user conversations for unread count
                    this.allUserConv.push(e.conversation);

                    // If this is the active conversation, add directly to message data
                    if (this.activeConversation?.id === conversation.id) {
                        this.messageData.push(e.conversation);
                        this.scrollToBottom();

                        // Clear typing indicator when a message is received
                        if (this.typing && this.typingUser?.id === e.conversation.sender) {
                            this.typing = false;
                            this.typingText = "";
                            this.allTyping[e.conversation.sender] = undefined;
                        }
                    }
                })
                .listenForWhisper('typing', (e) => {
                    console.log('Typing whisper received on channel', conversation.id, e);
                    this.handleTypingWhisper(e);
                });
        },
        handleTypingWhisper(e) {
            if (!e.typingUser || !e.typingUser.id) return;

            // Don't show typing indicator for current user's own messages
            if (e.typingUser.id === this.$page.props.user.id) return;

            console.log('Typing indicator from user:', e.typingUser.name);

            // Update typing indicators for all users
            this.allTyping = { ...this.allTyping, [e.typingUser.id]: e.typingText };

            // If this is for the active conversation, update the typing state
            if (this.activeConversation?.creator === e.typingUser.id) {
                this.typing = true;
                this.typingUser = e.typingUser;
                this.typingText = e.typingText;

                // Clear the typing indicator after delay
                clearTimeout(this.typingTimeout);
                this.typingTimeout = setTimeout(() => {
                    this.typing = false;
                    this.typingText = "";
                }, 4500);
            }

            // Clear from global typing list after delay
            setTimeout(() => {
                this.allTyping = { ...this.allTyping, [e.typingUser.id]: undefined };
            }, 4500);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messageContainer;
                if (el) {
                    el.scrollTo({
                        top: el.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        },
        sendMessage(formData) {
            if (!this.activeConversation?.id) {
                console.error('No active conversation selected');
                return;
            }

            // Add conversation ID to the form data
            formData.append('conversation_id', this.activeConversation.id);

            // Show loading or disable send button if needed
            // this.isSending = true; // Add this to data if implementing loading state

            axios.post('/support', formData)
                .then(response => {
                    if (response.data.success) {
                        // Add the message to the conversation
                        this.messageData.push(response.data.conversation);
                        this.scrollToBottom();
                    } else {
                        console.error('Error in response:', response.data);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    // Show an error notification or message to user
                    // this.$notify({ type: 'error', text: 'Failed to send message' });
                })
                .finally(() => {
                    // Reset loading state if implemented
                    // this.isSending = false;
                });
        },
        uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                alert('Only JPG, PNG, PDF and DOCX files are allowed');
                return;
            }

            const formData = new FormData();
            formData.append('conversation_id', this.activeConversation.id);
            formData.append('sender', this.$page.props.user.id);
            formData.append('message', ''); // Empty message for file-only uploads
            formData.append('attachment', file);

            // Using the standard support route for consistency
            axios.post('/support', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                if (response.data.success) {
                    this.messageData.push(response.data.conversation);
                    this.scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error uploading file:', error);
                // Show error notification
                // this.$notify({ type: 'error', text: 'Failed to upload file' });
            });
        },
        onImageDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('conversation_id', this.activeConversation.id);
            formData.append('sender', this.$page.props.user.id);
            formData.append('attachment', file);

            this.uploadFile({ target: { files: [file] } });
        },
        changeActiveChat(newConversation) {
            if (newConversation.id !== this.activeConversation.id) {
                // Leave the previous channel
                if (this.activeConversation?.id) {
                    // Proper cleanup
                    window.Echo.leave(`support.${this.activeConversation.id}`);
                }

                // Join the new channel
                this.channel = window.Echo.private(`support.${newConversation.id}`);

                // Listen for events on the new channel
                this.channel.listen('.SupportEvent', (e) => {
                    console.log('New conversation event received', e);
                    this.messageData.push(e.conversation);
                    this.scrollToBottom();
                }).listenForWhisper('typing', this.handleTypingWhisper);

                // Navigate to the new conversation
                this.$inertia.visit(`${route('support.index')}?conversation_id=${newConversation.id}`);

                // Reset unread count for this conversation
                if (this.supportIdList[newConversation.id]) {
                    this.supportIdList[newConversation.id] = 0;
                }
            }
        },
        handleTyping(text) {
            if (!this.channel || !text) return;

            // Throttle typing events to avoid excessive WebSocket traffic
            if (this.typingThrottleTimeout) return;

            // Send typing event to channel
            this.channel.whisper('typing', {
                typingUser: this.$page.props.user,
                typing: true,
                typingText: text,
            });

            // Set throttle timeout
            this.typingThrottleTimeout = setTimeout(() => {
                this.typingThrottleTimeout = null;
            }, 1000); // Only send typing event once per second max
        },
        handleContextMenu({ event, messageId }) {
            this.$refs.menu.open(event);
            this.clickedMessageId = messageId;
        },
        async doneConversation() {
            try {
                const ok = await this.confirm.show('Are you sure?');
                if (ok) {
                    await axios.put(route('support.update', this.activeConversation.id));
                    this.$inertia.visit(route('support.index'));
                }
            } catch (error) {
                console.error('Failed to mark conversation as done:', error);
            }
        },
        countDuplicate(array) {
            const counts = {};
            array.forEach(item => {
                const index = item.support_conversation_id;
                if (counts[index]) {
                    counts[index] += 1;
                } else {
                    counts[index] = 1;
                }
            });
            return counts;
        },
        isUserOnline(userId) {
            return this.onlineUser.some(user => user.id === userId);
        }
    },
    computed: {
        channel() {
            if (!this.activeConversation?.id) {
                return null;
            }
            return window.Echo.private(`support.${this.activeConversation.id}`);
        },
        supportIdList() {
            if (!Array.isArray(this.allUserConv)) {
                return {};
            }
            return this.countDuplicate(this.allUserConv);
        },
        filteredSupport() {
            if (!this.support || !Array.isArray(this.support)) {
                return [];
            }

            // Filter based on search term
            const filtered = this.searchTerm
                ? this.support.filter(conv =>
                    (conv.creator_name && conv.creator_name.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                    (conv.issues && conv.issues.toLowerCase().includes(this.searchTerm.toLowerCase()))
                  )
                : this.support;

            // Sort by online status - online users first
            return filtered.sort((a, b) => {
                const aIsOnline = this.onlineUser.some(user => user.id === a.creator);
                const bIsOnline = this.onlineUser.some(user => user.id === b.creator);

                if (aIsOnline && !bIsOnline) return -1;
                if (!aIsOnline && bIsOnline) return 1;

                // If both have same online status, sort by date
                return new Date(b.created_at || 0) - new Date(a.created_at || 0);
            });
        }
    },
    // Ensure proper cleanup when component is destroyed
    beforeUnmount() {
        // Leave all channels to prevent memory leaks
        if (this.activeConversation?.id) {
            window.Echo.leave(`support.${this.activeConversation.id}`);
        }
        window.Echo.leave('support');

        // Leave any other channels that might have been created
        if (this.support && Array.isArray(this.support)) {
            this.support.forEach(conversation => {
                if (conversation.id && conversation.id !== this.activeConversation?.id) {
                    window.Echo.leave(`support.${conversation.id}`);
                }
            });
        }
    }
}
</script>

<style>
.pswp__img--placeholder--blank {
    display: none !important;
}
.gallery-thumbnail img {
    object-fit: contain !important;
    max-width: 15rem!important;
    margin: 0!important;
}
.gallery-thumbnail {
    display: flex!important;
    margin: 0 0.2rem 0.09rem 0!important;
}
.pswp__bg {
    background-color: #fff !important;
}
.my-gallery {
    display: inline-flex!important;
}

/* Online indicator styles */
.user-online-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #4CAF50;
    display: inline-block;
    margin-right: 5px;
    box-shadow: 0 0 0 rgba(76, 175, 80, 0.6);
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.6);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(76, 175, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
}

/* Typing indicator styles */
.typing-indicator {
    border-top: 1px solid #e2e8f0;
    font-size: 0.875rem;
    background-color: #f9fafb;
    padding: 8px 12px;
    color: #4b5563;
}

/* Add animation for typing indicator */
.dot-animation {
    display: inline-block;
    animation: dotAnimation 1.4s infinite both;
    letter-spacing: 2px;
}

@keyframes dotAnimation {
    0% { opacity: 0.2; }
    20% { opacity: 1; }
    100% { opacity: 0.2; }
}

/* Search box styling */
.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group-append {
    display: flex;
    margin-left: -1px;
}

.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 0 0.25rem 0.25rem 0;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #b0b7c4;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #858c97;
}

/* Card enhancements */
.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.card-header {
    padding: 1rem;
}
</style>
