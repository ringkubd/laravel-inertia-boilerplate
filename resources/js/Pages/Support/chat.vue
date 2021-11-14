<template>
    <div>
        <button class="btn btn-primary c-chat-widget-button" ref="button" @click.prevent="toggleModal()">C <sup v-if="newMessage" class="text-center text-black">New</sup></button>
        <div id="chat" class="bg-gray-300">
            <div class="c-chat-widget" on ref="modal" :class="{show: modal.show}">
                <div class="c-chat-widget-header">
                    Chat with Support
                </div>
                <div class="c-chat-widget-container-main">
                    <ul ref="messageContainer" class="c-chat-widget-container">
                        <li @focus="seenMessage" :messageId="mess.id" @contextmenu="rightClick" class="border-2 p-2" :class="$page.props.user.id === mess.sender.id ? 'text-left' : 'text-right'" v-for="(mess, index) in messageData" :key={index}>
                            <span class="text-green-900 inline-block border-b-2 border-blue-200">
                                <b>{{mess.sender.name}}  <small style="font-size: 8px; font-style: italic;" class="text-blue-400 pull-right">{{moment(mess.created_at).fromNow() }}</small></b>
                            </span>
                            <br>
                            {{ mess.message }}
                        </li>
                    </ul>
                    <div class="c-chat-widget-footer">
                        <span v-if="typing"><b>{{typingUser.name}}: is typing ... </b>{{typingText}}</span>
                        <form action="" @submit.prevent="sendMessage">
                            <input type="text" class="c-chat-widget-text form-controller" @keydown="isTyping" v-model="message">
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
        }
    },
    created: function () {
        this.moment = moment;
        if (this.$page.props.active_support?.id !== undefined){
            this.initialize()
        }
        this.onlineChannel.joining(user => {
            console.log(user)
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
                    // remove is typing indicator after 0.9s
                    _this.showModal()
                    setTimeout(function() {
                        _this.typing = false
                    }, 6000);
                });
            this.channelObj = channelObj;
        },
        sendMessage() {
            if(this.message.replace(/^\s+|\s+$/g, "") === ""){
                return;
            }

            axios.post(route('support.store'), {
                message: this.message,
                conversation_id: this.conversationId
            }).then(res => {
                this.messageData.push(res.data.conversation)
                this.scrollToBottom()
            })
            this.message = "";
        },
        toggleModal() {
            this.initialize().then(res => {
                this.initChannel()
            })

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
        }
    },
    computed:{
        onlineChannel(){
            return this.onlineChannel = window.Echo.join('support')
        }
    }
}
</script>
