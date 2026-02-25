<template>
    <div>
        <button class="c-chat-widget-button" :class="newMessage ? 'animate-pulse-glow bg-red-500' : 'bg-gradient-to-r from-indigo-600 to-blue-600'" ref="button" @click.prevent="toggleModal()">
            <span class="flex items-center justify-center h-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </span>
            <span v-if="newMessage" class="absolute -top-1 -right-1 flex justify-center items-center animate-ping-slow">
                <span class="absolute inline-flex h-5 w-5 rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
        </button>
        <div id="chat" @drop="onImageDrop">
            <div class="c-chat-widget" ref="modal" :class="{show: modal.show}" @drop="onImageDrop">
                <div class="c-chat-widget-header flex items-center justify-between bg-gradient-to-r from-indigo-600 to-blue-600 p-3 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-white opacity-10">
                        <svg class="w-64 h-64 absolute -top-32 -left-32 text-white opacity-10" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" d="M45,-67.2C58,-62,68.2,-47.4,74.2,-31.9C80.2,-16.3,82,0.2,78,15.2C73.9,30.2,64.1,43.6,51.5,51.5C38.9,59.5,23.4,62,8.6,63.1C-6.3,64.2,-20.4,64,-32.4,58.6C-44.4,53.1,-54.2,42.4,-61.9,29.4C-69.7,16.4,-75.2,1.2,-73.4,-13.2C-71.6,-27.6,-62.5,-41.2,-50.4,-46.7C-38.3,-52.2,-23.2,-49.6,-8.5,-51.9C6.1,-54.1,31.9,-72.3,45,-67.2Z" transform="translate(100 100)" />
                        </svg>
                    </div>
                    <div class="flex items-center z-10">
                        <div class="h-2 w-2 rounded-full bg-green-400 mr-2 animate-pulse"></div>
                        <div class="text-white font-bold">Chat with Support</div>
                    </div>
                    <div class="text-white cursor-pointer hover:bg-white hover:bg-opacity-20 rounded-full w-7 h-7 flex items-center justify-center transition-colors duration-200 z-10" @click="hideModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="c-chat-widget-container-main" @drop="onImageDrop">
                    <div class="py-2 px-3 bg-gray-50 border-b flex items-center justify-center">
                        <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            Today's Conversation
                        </div>
                    </div>
                    <ul ref="messageContainer" class="c-chat-widget-container">
                        <li v-if="!messageData || messageData.length === 0" class="flex items-center justify-center h-full">
                            <div class="text-center p-6">
                                <div class="bg-gray-100 rounded-full p-4 inline-block mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500">No messages yet</p>
                                <p class="text-gray-400 text-xs mt-1">Your conversation will appear here</p>
                            </div>
                        </li>
                        <li v-else
                            @focus="seenMessage"
                            :messageId="mess.id"
                            @contextmenu="rightClick"
                            class="relative mb-4 transition-all duration-200 group"
                            :class="$page.props.user.id === mess.sender.id ? 'text-left pl-2 pr-8' : 'text-right pr-2 pl-8'"
                            v-for="(mess, index) in messageData"
                            :key="index">

                            <div class="max-w-[80%] inline-block">
                                <div :class="$page.props.user.id === mess.sender.id ?
                                    'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-tr-xl rounded-bl-xl rounded-br-xl shadow-md' :
                                    'bg-white border border-gray-200 rounded-tl-xl rounded-bl-xl rounded-br-xl shadow-sm text-left'"
                                    class="p-3 mb-1 relative message-wrapper">

                                    <div class="flex justify-between mb-1 text-xs">
                                        <span :class="$page.props.user.id === mess.sender.id ? 'text-blue-100' : 'text-blue-600'">
                                            {{mess.sender.name}}
                                        </span>
                                        <span :class="$page.props.user.id === mess.sender.id ? 'text-blue-100' : 'text-gray-400'">
                                            {{moment(mess.created_at).format('h:mm a')}}
                                        </span>
                                    </div>

                                    <div class="message-content">
                                        <img v-if="mess.message === '@like@'" src="/like.png" class="image w-12 h-12 inline-block">
                                        <p v-else :class="$page.props.user.id === mess.sender.id ? 'text-white' : 'text-gray-700'">{{mess.message}}</p>

                                        <div class="mt-2" v-if="mess.attachment_type">
                                            <div v-if="String(mess.attachment_type).search('image') !== -1"
                                                class="rounded-lg overflow-hidden border bg-gray-100 inline-block">
                                                <img :src="mess.attachment" class="max-w-full max-h-48 object-contain">
                                            </div>
                                            <a v-else-if="mess.attachment_type !== null"
                                                :href="mess.attachment"
                                                target="_blank"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center transition-colors duration-200"
                                                :class="$page.props.user.id === mess.sender.id ?
                                                    'text-blue-100 bg-blue-600 hover:bg-blue-700' :
                                                    'text-white bg-blue-600 hover:bg-blue-700'">
                                                <font-awesome-icon icon="paperclip" size="md" class="mr-2"/>
                                                View Attachment
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-2">
                                    <small :class="$page.props.user.id === mess.sender.id ? 'text-gray-400' : 'text-gray-400 float-right'">
                                        {{moment(mess.created_at).calendar(null, {
                                            sameDay: '[Today]',
                                            lastDay: '[Yesterday]',
                                            lastWeek: 'dddd',
                                            sameElse: 'MMM D'
                                        })}}
                                    </small>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="c-chat-widget-footer">
                        <div v-if="typing" class="typing-indicator rounded bg-gray-100/80 backdrop-blur-sm p-2 mx-2 mb-1 shadow-sm border border-gray-200/50">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 mr-2">
                                    {{ typingUser.name ? typingUser.name.charAt(0) : '?' }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">{{typingUser.name}}</span>
                                    <div class="text-gray-600 text-xs flex items-center">
                                        is typing
                                        <div class="ml-1 flex space-x-1">
                                            <div class="w-1 h-1 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0ms"></div>
                                            <div class="w-1 h-1 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 150ms"></div>
                                            <div class="w-1 h-1 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 300ms"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="" @submit.prevent="sendMessage" class="px-3 pt-2 pb-3 bg-white border-t border-gray-100 shadow-sm">
                            <div class="form-group mb-2">
                                <textarea
                                    class="c-chat-widget-text w-full resize-none rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all duration-200 placeholder-gray-400"
                                    @keydown="isTyping"
                                    @keydown.enter.exact.prevent="sendMessage"
                                    @input="autoResizeTextarea"
                                    @paste="autoResizeTextarea"
                                    ref="messageInput"
                                    v-model="message"
                                    style="min-height: 40px; max-height: 200px; overflow-y: auto;"
                                    placeholder="Type your message here..."></textarea>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="file-upload-container group">
                                    <label for="attachment" class="cursor-pointer flex items-center text-gray-500 hover:text-blue-600 transition-all duration-150 py-1 px-2 rounded-lg hover:bg-blue-50 group-hover:bg-blue-50">
                                        <font-awesome-icon icon="paperclip" size="md" class="mr-2"/>
                                        <span class="text-sm truncate max-w-[150px] inline-block" v-html="fileName === 'Attach you files here' ? 'Add attachment' : fileName"></span>
                                    </label>
                                    <input type="file" id="attachment" class="hidden" @change="onFileSelect" ref="attachment">
                                </div>

                                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-medium py-2 px-5 rounded-lg transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                                    <span>Send</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform rotate-90 -mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faCopy, faPaperclip } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faCopy, faPaperclip);

export default {
    props: [],
    data() {
        return {
            modal: {
                show: false,
            },
            message: '',
            messageData: {},
            collapsed: false,
            channel: null,
            channelObj: Object,
            conversationId : this.$page.props.active_support?.id,
            newMessage: false,
            typing: false,
            typingUser: "",
            typingText: "",
            supportOnline: [],
            previewAttachment: "",
            fileName: "Attach you files here",
            typingTimeout: null,
            typingThrottleTimeout: null
        }
    },
    created: function () {
        this.moment = moment;
        if (this.$page.props.active_support?.id !== undefined){
            this.initialize()
        }
        this.onlineChannel.joining(user => {
            this.supportOnline.push(user)
        })
    },
    mounted() {
        if (this.$page.props.active_support?.id !== undefined){
            console.log('Initializing chat')
            this.initChannel()
        }

        // Initialize textarea height when component mounts
        this.$nextTick(() => {
            if (this.$refs.messageInput) {
                this.autoResizeTextarea();
            }
        });
    },

    watch: {
        // Watch for message clearing to reset textarea height
        message(newVal) {
            if (newVal === '') {
                const textarea = this.$refs.messageInput;
                if (textarea) {
                    textarea.style.height = '40px'; // Reset to default height
                }
            }
        }
    },
    methods: {
        async initialize(){
            return await axios.get(route('get_support_active_conversation'))
                .then(res => {
                    let data = res.data
                    this.channel = "support."+data.id
                    this.conversationId = data.id
                    this.messageData = data.message === undefined ? [] : data.message
                    return Promise.resolve(res)
                })
        },
        initChannel(){
            // First clean up any existing connections
            if (this.channelObj) {
                window.Echo.leave(`support.${this.conversationId}`);
            }

            let _this = this;
            let channelObj = window.Echo.private(`support.${this.conversationId}`);
            let audio = new Audio('/beep-2.mp3')

            // Listen for new messages
            channelObj.listen('.SupportEvent', (e) => {
                console.log('Message received', e.conversation)

                // Don't add own messages twice (they're already added in the sendMessage method)
                if (e.conversation.sender.id !== this.$page.props.user.id) {
                    this.messageData.push(e.conversation)
                    this.newMessage = true
                    this.typing = false
                    audio.load()
                    audio.play()
                    this.scrollToBottom()
                }
            })

            // Listen for typing indicators
            .listenForWhisper('typing', (e) => {
                console.log('Typing whisper received', e);

                // Don't show our own typing indicator
                if (e.typingUser.id === this.$page.props.user.id) return;

                this.typingUser = e.typingUser;
                this.typing = e.typing;
                this.typingText = e.typingText;
                _this.showModal()

                // Clear previous timeout if exists
                clearTimeout(this.typingTimeout)

                // Set new timeout
                this.typingTimeout = setTimeout(function() {
                    _this.typing = false
                    _this.typingText = "";
                }, 6000);
            });

            this.channelObj = channelObj;
        },
        sendMessage() {
            let files = this.$refs.attachment.files
            if(this.message.replace(/^\s+|\s+$/g, "") === "" && files.length === 0){
                return;
            }
            let formData = new FormData();
            if (files.length > 0) {
                formData.append('attachment', files[0]);
            }
            formData.append('message', this.message)
            formData.append('conversation_id', this.conversationId)

            axios.post(route('support.store'), formData).then(res => {
                this.messageData.push(res.data.conversation)
                this.scrollToBottom()
            }).catch(error => {
                console.error('Error sending message:', error)
            })
            this.message = "";
            this.fileName = "Attach you files here"
            this.$refs.attachment.value = null
        },
        toggleModal() {
            this.initialize().then(res => {
                if (this.$page.props.active_support?.id === undefined){
                    this.initChannel()
                }
            })
            this.newMessage = false
            this.modal.show = !this.modal.show;
            this.scrollToBottom()
        },
        showModal() {
            this.modal.show = true;
            this.scrollToBottom()
        },
        hideModal() {
            this.modal.show = false;
            this.scrollToBottom()
        },
        scrollToBottom(){
            const el = this.$refs.messageContainer
            if (el) {
                el.scrollTo({
                    top: el.scrollHeight,
                    behavior: 'smooth'
                });
            }
        },
        rightClick(e){
            //e.preventDefault()
            console.log(e)
        },
        isTyping(){
            let _this = this

            // Don't send typing events too frequently
            if (this.typingThrottleTimeout) return;

            setTimeout(function() {
                _this.channelObj.whisper('typing', {
                    typingUser: _this.$page.props.user,
                    typing: true,
                    typingText: _this.message,
                });
            }, 300);

            // Set throttle timeout
            this.typingThrottleTimeout = setTimeout(() => {
                this.typingThrottleTimeout = null;
            }, 1000); // Only send typing event once per second max
        },
        seenMessage(){
            console.log(this)
        },
        onImageDrop(e){
            e.preventDefault()
            let files = [...e.dataTransfer.files]
            if (files.length > 0) {
                let attachment = [];
                let formData = new FormData();
                formData.append('attachment', files[0]);
                formData.append('message', this.message)
                formData.append('conversation_id', this.conversationId)

                axios.post(route('support.store'), formData).then(res => {
                    this.messageData.push(res.data.conversation)
                    this.scrollToBottom()
                })
                this.message = "";
            }
        },
        onFileSelect(){
            this.fileName = this.$refs.attachment?.files.length > 0 ? this.$refs.attachment.files[0].name : "Attach you files here"
        },
        autoResizeTextarea(event) {
            const textarea = this.$refs.messageInput;
            if (!textarea) return;

            // For paste events, add a small delay to ensure content is fully processed
            if (event && event.type === 'paste') {
                setTimeout(() => this.performResize(textarea), 10);
            } else {
                this.performResize(textarea);
            }
        },

        performResize(textarea) {
            // Reset height to auto first to get accurate scrollHeight
            textarea.style.height = 'auto';

            // Calculate new height with some padding to prevent scrollbars
            const newHeight = Math.min(Math.max(textarea.scrollHeight, 40), 200); // Min 40px, max 200px
            textarea.style.height = `${newHeight}px`;

            // Scroll chat container to bottom when textarea grows
            if (newHeight > 40) {
                this.scrollToBottom();
            }
        }
    },
    computed:{
        onlineChannel(){
            return this.onlineChannel = window.Echo.join('support')
        }
    },
    beforeUnmount() {
        // Leave the channel for this conversation
        if (this.conversationId) {
            window.Echo.leave(`support.${this.conversationId}`);
        }

        // Leave the general support channel
        window.Echo.leave('support');

        // Clear all timeouts to prevent memory leaks
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        if (this.typingThrottleTimeout) {
            clearTimeout(this.typingThrottleTimeout);
        }
    }
}
</script>

<style>
.c-chat-widget-button{
    border-radius: 100%;
    color: white;
    font-weight: 700;
    font-size: 20px;
    width: 56px;
    height: 56px;
    position: fixed;
    bottom: 40px;
    right: 20px;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.25);
    z-index: 10000;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    overflow: hidden;
}

.c-chat-widget-button:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 50%);
    border-radius: 100%;
}

.c-chat-widget-button:hover {
    transform: scale(1.08) translateY(-3px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
}

.c-chat-widget{
    position: fixed;
    background: white;
    right: 30px;
    bottom: -100vh;
    max-height: 550px;
    z-index: 10000;
    width: 380px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
    border-radius: 10px;
    overflow: hidden;
    transition: bottom ease-in-out 0.3s;
}

.c-chat-widget.show{
    bottom: 100px;
}

.c-chat-widget-header{
    color: white;
    align-content: center;
}

.c-chat-widget-container-main{
    height: 500px;
    position: relative;
}

.c-chat-widget-container {
    min-height: 360px;
    max-height: 360px;
    display: flex;
    flex-direction: column;
    overflow: auto;
    padding: 12px;
    background-color: #f9fafb;
    scrollbar-width: thin;
}

.c-chat-widget-text{
    width: 100%;
    resize: none;
    height: 70px;
    padding: 10px;
    font-size: 14px;
}

.c-chat-widget-footer{
    position: absolute;
    bottom: 0;
    width: 100%;
    background-color: white;
    border-top: 1px solid #e5e7eb;
    padding-top: 8px;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
    40% {transform: translateY(-20px);}
    60% {transform: translateY(-10px);}
}

.animate-bounce {
    animation: bounce 2s infinite;
}

.typing-indicator {
    font-size: 0.875rem;
    margin-bottom: 8px;
}

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

@keyframes blink {
    0% { opacity: 0.2; }
    20% { opacity: 1; }
    100% { opacity: 0.2; }
}

.typing-indicator::after {
    content: '...';
    display: inline-block;
    animation: blink 1.4s infinite both;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 5px;
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

/* Message styling */
.message-content {
    word-break: break-word;
}

/* Add fade-in animation for new messages */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.c-chat-widget-container li {
    animation: fadeIn 0.3s ease-out;
}

/* File upload container styling */
.file-upload-container {
    font-size: 14px;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.file-upload-container:hover {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>
