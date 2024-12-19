<template>
    <Head>
        <title>Chat - {{ conversationName }}</title>
    </Head>
    <breeze-authenticated-layout>
        <template #header>
            <div class="flex justify-between items-center">
                <PageHeader>{{ conversationName }}</PageHeader>
                <div v-if="conversation && conversation.type === 'group'" class="flex items-center">
                    <button @click="showParticipants = true" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="mr-2">{{ conversation.participants.length }} Participants</span>
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <div class="flex h-screen antialiased text-gray-800">
            <div class="flex flex-row h-full w-full overflow-x-hidden">
                <!-- Sidebar with conversations list -->
                <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
                    <div class="flex flex-row items-center justify-center h-12 w-full">
                        <div class="flex items-center justify-center rounded-2xl text-indigo-700 bg-indigo-100 h-10 w-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <div class="ml-2 font-bold text-2xl">QuickChat</div>
                    </div>

                    <!-- New Chat Button -->
                    <button @click="showNewChat = true" class="flex items-center justify-center h-12 w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Chat
                    </button>

                    <!-- Conversations List -->
                    <div class="flex flex-col mt-8">
                        <div class="flex flex-row items-center justify-between text-xs">
                            <span class="font-bold">Conversations</span>
                            <span class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full">{{ conversations.length }}</span>
                        </div>
                        <div class="flex flex-col space-y-1 mt-4 -mx-2 overflow-y-auto">
                            <button v-for="chat in conversations" 
                                    :key="chat.id"
                                    @click="selectConversation(chat.id)"
                                    class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
                                    :class="{ 'bg-gray-100': chat.id === conversation?.id }">
                                <div class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full">
                                    {{ firstLetter(chat.name) }}
                                </div>
                                <div class="ml-2 text-sm font-semibold">
                                    {{ isOneToOne(chat) ? getOtherParticipantName(chat) : chat.name }}
                                    <div class="text-xs text-gray-500">
                                        {{ chat.last_message?.body || 'No messages yet' }}
                                    </div>
                                </div>
                                <div v-if="chat.unread_count > 0" 
                                     class="flex items-center justify-center ml-auto text-xs text-white bg-red-500 h-4 w-4 rounded-full">
                                    {{ chat.unread_count }}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex flex-col flex-auto h-full p-6">
                    <div class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4">
                        <!-- Messages -->
                        <div class="flex flex-col h-full overflow-x-auto mb-4" ref="messagesContainer">
                            <div class="flex flex-col h-full">
                                <div class="grid grid-cols-12 gap-y-2">
                                    <div v-for="message in messages" :key="message.id"
                                         :class="[message.sender.id === $page.props.user.id ? 'col-start-6 col-end-13' : 'col-start-1 col-end-8']">
                                        <div class="flex flex-row items-center" 
                                             :class="[message.sender.id === $page.props.user.id ? 'justify-end' : '']">
                                            <div class="relative mr-3" :class="[message.sender.id === $page.props.user.id ? 'order-1' : 'order-2']">
                                                <div class="relative text-sm py-2 px-4 shadow rounded-xl" 
                                                     :class="[message.sender.id === $page.props.user.id ? 'bg-indigo-100' : 'bg-white']">
                                                    <div>{{ message.body }}</div>
                                                    <div class="absolute bottom-0" 
                                                         :class="[message.sender.id === $page.props.user.id ? 'right-0 -mb-5' : 'left-0 -mb-5']">
                                                        <span class="text-xs text-gray-500">
                                                            {{ formatTime(message.created_at) }}
                                                            <span v-if="message.sender.id === $page.props.user.id" class="ml-1">
                                                                <span v-if="message.status === 'sent'" class="text-gray-400">✓</span>
                                                                <span v-else-if="message.status === 'delivered'" class="text-blue-400">✓✓</span>
                                                                <span v-else-if="message.status === 'read'" class="text-green-400">✓✓</span>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4">
                            <div class="flex-grow">
                                <div class="relative w-full">
                                    <input
                                        v-model="newMessage"
                                        type="text"
                                        class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                                        placeholder="Type your message..."
                                        @keyup.enter="sendMessage"
                                        @input="isTyping"
                                    />
                                </div>
                            </div>
                            <div class="ml-4">
                                <button
                                    class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
                                    @click="sendMessage"
                                >
                                    <span>Send</span>
                                    <span class="ml-2">
                                        <svg
                                            class="w-4 h-4 transform rotate-45 -mt-px"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                            ></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Chat Modal -->
        <Modal :show="showNewChat" @close="showNewChat = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Start New Conversation</h2>
                <div class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select v-model="newChatData.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="personal">Personal</option>
                                <option value="group">Group</option>
                                <option value="support">Support</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" v-model="newChatData.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Participants</label>
                            <select v-model="newChatData.participants" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="showNewChat = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button @click="createNewChat" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Participants Modal -->
        <Modal :show="showParticipants" @close="showParticipants = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Conversation Participants</h2>
                <div class="mt-6">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="participant in conversation?.participants" :key="participant.id" class="py-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-200 flex items-center justify-center">
                                        {{ firstLetter(participant.name) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ participant.name }}</p>
                                    <p class="text-sm text-gray-500">{{ participant.email }}</p>
                                </div>
                            </div>
                            <button v-if="conversation.creator === $page.props.user.id && participant.id !== $page.props.user.id"
                                    @click="removeParticipant(participant.id)"
                                    class="ml-4 text-red-600 hover:text-red-900">
                                Remove
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </Modal>
    </breeze-authenticated-layout>
</template>

<script>
import { ref, onMounted, nextTick } from 'vue'
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated"
import PageHeader from "@/Shared/PageHeader"
import Modal from "@/Shared/Modal"
import axios from 'axios'
import moment from 'moment'

export default {
    components: {
        BreezeAuthenticatedLayout,
        PageHeader,
        Modal
    },

    props: {
        conversations: {
            type: Array,
            default: () => []
        },
        conversation: {
            type: Object,
            default: null
        }
    },

    setup(props) {
        const newMessage = ref('')
        const messages = ref(props.conversation?.messages || [])
        const showNewChat = ref(false)
        const showParticipants = ref(false)
        const messagesContainer = ref(null)
        const typingTimeout = ref(null)
        const newChatData = ref({
            type: 'personal',
            name: '',
            participants: []
        })

        const scrollToBottom = async () => {
            await nextTick()
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
            }
        }

        onMounted(() => {
            scrollToBottom()
            Echo.private(`conversation.${props.conversation?.id}`)
                .listen('MessageEvent', (e) => {
                    messages.value.push(e.message)
                    scrollToBottom()
                })
                .listenForWhisper('typing', (e) => {
                    // Handle typing indicator
                })
        })

        return {
            newMessage,
            messages,
            showNewChat,
            showParticipants,
            messagesContainer,
            typingTimeout,
            newChatData,
            scrollToBottom
        }
    },

    methods: {
        firstLetter(str) {
            return str ? str.charAt(0).toUpperCase() : ''
        },

        formatTime(time) {
            return moment(time).format('HH:mm')
        },

        isOneToOne(conversation) {
            return conversation.type === 'personal';
        },

        getOtherParticipantName(conversation) {
            const currentUserId = this.$page.props.user.id;
            return conversation.participants.find(p => p.id !== currentUserId).name;
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return

            try {
                const response = await axios.post('/conversations', {
                    conversation_id: this.conversation.id,
                    sender: this.$page.props.user.id,
                    body: this.newMessage
                })

                this.messages.push(response.data.message)
                this.newMessage = ''
                this.scrollToBottom()
            } catch (error) {
                console.error('Error sending message:', error)
            }
        },

        async selectConversation(id) {
            window.location.href = `/conversations/${id}`
        },

        isTyping() {
            if (this.typingTimeout) {
                clearTimeout(this.typingTimeout)
            }

            Echo.private(`conversation.${this.conversation.id}`)
                .whisper('typing', {
                    user: this.$page.props.user.name
                })

            this.typingTimeout = setTimeout(() => {
                this.typingTimeout = null
            }, 3000)
        },

        async createNewChat() {
            try {
                const response = await axios.post('/conversations/create', this.newChatData)
                this.showNewChat = false
                window.location.href = `/conversations/${response.data.id}`
            } catch (error) {
                console.error('Error creating conversation:', error)
            }
        },

        async removeParticipant(userId) {
            try {
                await axios.delete(`/conversations/${this.conversation.id}/participants/${userId}`)
                this.conversation.participants = this.conversation.participants.filter(p => p.id !== userId)
            } catch (error) {
                console.error('Error removing participant:', error)
            }
        }
    },

    computed: {
        conversationName() {
            if (!this.conversation) return 'Chat'
            return this.isOneToOne(this.conversation) ? this.getOtherParticipantName(this.conversation) : this.conversation.name
        }
    }
}
</script>
<style scoped>
.message {
    padding: 10px;
    margin: 5px 0;
    border-radius: 15px;
    background-color: #f0f0f0;
    max-width: 80%;
}

.message.sent {
    background-color: #d1e7dd;
    align-self: flex-end;
}

.message.received {
    background-color: #f8d7da;
    align-self: flex-start;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow-y: auto;
    padding: 10px;
}

.input-container {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ccc;
}

input[type="text"] {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
}

button {
    padding: 10px 20px;
    background-color: #6f42c1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #5a3791;
}
</style>