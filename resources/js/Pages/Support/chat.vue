<template>
    <div>
        <button class="c-chat-widget-button" :class="newMessage ? 'animate-bounce bg-red-500' : 'bg-green-500'" ref="button" @click.prevent="toggleModal()">C <sup v-if="newMessage" class="text-center text-black">New</sup></button>
        <div id="chat" class="bg-gray-300" @drop="onImageDrop">
            <div class="c-chat-widget" on ref="modal" :class="{show: modal.show}" @drop="onImageDrop">
                <div class="c-chat-widget-header row m-auto">
                    <div class="col-11 text-left">Chat with Support</div>
                    <div class="col-1 text-right text-black-500 block border-2 backdrop-blur-2xl cursor-pointer" @click="hideModal">X</div>
                </div>
                <div class="c-chat-widget-container-main" @drop="onImageDrop">
                    <ul ref="messageContainer" class="c-chat-widget-container">
                        <li @focus="seenMessage" :messageId="mess.id" @contextmenu="rightClick" class="border-2 py-2 my-0.5 shadow-lg shadow-inner" :class="$page.props.user.id === mess.sender.id ? 'text-left' : 'text-right'" v-for="(mess, index) in messageData" :key={index}>
                            <span class="text-green-900 inline-block border-b-2 border-blue-200">
                                <b>{{mess.sender.name}}  <small style="font-size: 8px; font-style: italic;" class="text-blue-400 pull-right">{{moment(mess.created_at).fromNow() }}</small></b>
                            </span>
                            <br>
                            <img v-if="mess.message === '@like@'" src="/like.png" class="image w-1/12 flex content-center">
                            <span v-else>{{mess.message}}</span>
                            <img v-if="String(mess.attachment_type).search('image') !== -1" :src="mess.attachment" class="img-thumbnail image w-1/2 content-center">
                            <a  v-else-if="mess.attachment_type !== null" :href="mess.attachment" target="_blank">
                                <font-awesome-icon
                                    icon="paperclip"
                                    size="md"
                                    rotation="rotate"
                                    class="text-info"
                                ></font-awesome-icon>
                                Attachment
                            </a>
                        </li>
                    </ul>
                    <div class="c-chat-widget-footer">
                        <span v-if="typing"><b>{{typingUser.name}}: is typing ... </b>{{typingText}}</span>
                        <form action="" @submit.prevent="sendMessage">
                            <div class="form-group">
                                <textarea class="c-chat-widget-text form-controller" @keydown="isTyping" v-model="message"> </textarea>
                            </div>
                            <div class="form-group mb-1">
                                <div class="bg-white px-2">
                                    <div class="max-w-md mx-auto rounded-lg overflow-hidden md:max-w-xl">
                                        <div class="md:flex">
                                            <div class="w-full">
                                                <div class="relative border-dotted h-10 bg-gray-100 flex justify-center items-center">
                                                    <div class="absolute">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fa fa-folder-open fa-4x text-blue-700"></i>
                                                            <span class="block text-gray-400 font-normal" v-html="fileName"></span>
                                                        </div>
                                                    </div>
                                                    <input type="file" class="h-full w-full opacity-0" @change="onFileSelect" name="" ref="attachment">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-success pull-right" value="Send">
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
            fileName: "Attach you files here"
        }
    },
    created: function () {
        this.moment = moment;
        if (this.$page.props.active_support?.id !== undefined){
            this.initialize()
        }
        this.onlineChannel.joining(user => {
            this.supportOnline.unshift(user)
        })
    },
    mounted() {
        if (this.$page.props.active_support?.id !== undefined){
            this.initChannel()
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
            let _this = this;
            let channelObj = window.Echo.private("support."+this.conversationId);
            let audio = new Audio('beep-2.mp3')
            channelObj.listen('SupportEvent', (e) => {
                this.messageData.push(e.conversation)
                this.newMessage = true
                this.typing = false
                audio.load()
                audio.play()
            }).listenForWhisper('typing', (e) => {
                this.typingUser = e.typingUser;
                this.typing = e.typing;
                this.typingText = e.typingText;
                _this.showModal()
                setTimeout(function() {
                    _this.typing = false
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
            })
            this.message = "";
            this.fileName = "Attach you files here"
            this.$refs.attachment.value = null
        },
        toggleModal() {
            this.initialize().then(res => {
                this.initChannel()
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
            el.scrollTop = el.scrollHeight;
        },
        rightClick(e){
            //e.preventDefault()
            console.log(e)
        },
        isTyping(){
            let _this = this
            setTimeout(function() {
                _this.channelObj.whisper('typing', {
                    typingUser: _this.$page.props.user,
                    typing: true,
                    typingText: _this.message,
                });
            }, 300);
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
        }
    },
    computed:{
        onlineChannel(){
            return this.onlineChannel = window.Echo.join('support')
        }
    }
}
</script>
