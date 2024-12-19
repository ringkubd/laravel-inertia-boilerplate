<template>
    <Head>
        <title>Support Management | IsDB-BISEW</title>
    </Head>
    <Authenticated>
        <div class="container">
            <div class="row h-screen">
                <div class="col-md-4">
                    <div class="card mt-1" style="height: 85vh!important;">
                        <div class="card-header">
                            <h4>Active Request List</h4>
                        </div>
                        <div class="card-body chat overflow-scroll" style="height: 58vh!important;">
                            <ChatUserList 
                                :support="support"
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
                    <div class="card" style="height: 85vh!important;">
                        <div class="card-header">
                            <h2>{{ activeConversation?.creator?.name }}</h2>
                        </div>
                        <div class="card-body overflow-scroll" ref="messageContainer" style="height: 58vh!important;">
                            <ChatMessages 
                                :messages="messageData"
                                :conversation-creator-id="activeConversation?.creator?.id"
                                @message-seen="seenMessage"
                                @context-menu="handleContextMenu"
                            />
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
                    <div class="bg-gray-200 w-36 border border-gray-300 rounded-lg flex flex-col text-sm text-gray-500">
                        <div class="flex hover:bg-red-300 my-2 py-2 px-2 rounded">
                            <jet-button type="submit" @click="deleteMessage">
                                <font-awesome-icon icon="trash" size="md" class="text-danger"/>
                                Delete
                            </jet-button>
                        </div>
                        <div class="flex hover:bg-gray-100 my-2 py-2 px-2 rounded">
                            <div class="text-gray-900" @click="copyMessage">
                                <font-awesome-icon icon="copy" size="md" class="text-info"/>
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
    faThumbsUp, faCheck, faDotCircle 
} from "@fortawesome/free-solid-svg-icons";

library.add(faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck, faDotCircle);

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
            onlineUser: []
        }
    },
    mounted() {
        this.setupWebSocketListeners();
        this.scrollToBottom();
        // Ensure isOnline is defined or remove if not necessary
        if (typeof this.isOnline === 'function') {
            this.isOnline();
        }
        this.confirm = useConfirm();
        this.messageData = this.activeConversation?.message;
    },
    methods: {
        setupWebSocketListeners() {
            Echo.private(`support.${this.activeConversation?.id}`)
                .listen('SupportEvent', (e) => {
                    console.log(e.message);
                    this.messageData.push(e.message);
                    this.scrollToBottom();
                })
                .listenForWhisper('typing', this.handleTypingWhisper);
        },
        setupConversationChannel(conversation) {
            this.supportIdList[conversation.id] = 0;
            window.Echo.private(`support.${conversation.id}`)
                .listen('SupportEvent', (e) => {
                    this.allUserConv.push(e.conversation);
                })
                .listenForWhisper('typing', (e) => {
                    this.handleTypingWhisper(e);
                });
        },
        handleTypingWhisper(e) {
            this.allTyping[e.typingUser.id] = e.typingText;
            setTimeout(() => {
                this.allTyping = { ...this.allTyping, [e.typingUser.id]: undefined };
            }, 4500);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messageContainer;
                el.scrollTop = el.scrollHeight;
            });
        },
        sendMessage(formData) {
            console.log(formData);
            // if (!formData || typeof formData.message !== 'string' || !formData.message.trim()) return;
            formData.append('conversation_id', this.activeConversation.id);
            axios.post('/support', formData)
                .then(response => {
                    this.messageData.push(response.data.conversation);
                    this.scrollToBottom();
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
        },
        uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('conversation_id', this.activeConversation.id);
            formData.append('sender', this.$page.props.user.id);
            formData.append('attachment', file);

            axios.post('/support/messages', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                this.messageData.push(response.data.conversation);
                this.scrollToBottom();
            })
            .catch(error => {
                console.error('Error uploading file:', error);
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
                window.Echo.leave(`support.${this.activeConversation.id}`);
                this.channel = window.Echo.private(`support.${newConversation.id}`);
                this.$inertia.visit(`${route('support.index')}?conversation_id=${newConversation.id}`);
            }
        },
        handleTyping(text) {
            this.channel.whisper('typing', {
                typingUser: this.$page.props.user,
                typing: true,
                typingText: text,
            });
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
    },
    computed: {
        channel() {
            return window.Echo.private(`support.${this.activeConversation.id}`);
        },
        supportIdList() {
            return this.countDuplicate(this.allUserConv);
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
</style>
