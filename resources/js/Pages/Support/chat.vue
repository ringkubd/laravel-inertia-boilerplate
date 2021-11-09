<template>
    <div>
        <button class="btn btn-primary c-chat-widget-button" ref="button" @click.prevent="toggleModal()">C</button>
        <div id="chat" class="bg-gray-300">
            <div class="c-chat-widget" on ref="modal" :class="{show: modal.show}">
                <div class="c-chat-widget-header">
                    Chat with Support
                </div>
                <div class="c-chat-widget-container-main">
                    <ul ref="messageContainer" class="c-chat-widget-container">
                        <li @contextmenu="rightClick" class="border-2 p-2" :class="$page.props.user.id === mess.sender.id ? 'text-left' : 'text-right'" v-for="(mess, index) in messageData" :key={index}>
                            <span class="text-green-900 inline-block border-b-2 border-blue-200">
                                <b>{{mess.sender.name}}  <small style="font-size: 8px; font-style: italic;" class="text-blue-400 pull-right">{{moment(mess.created_at).fromNow() }}</small></b>
                            </span>
                            <br>
                            {{ mess.message }}
                        </li>
                    </ul>
                    <div class="c-chat-widget-footer">
                        <form action="" @submit.prevent="sendMessage">
                            <input type="text" class="c-chat-widget-text form-controller" v-model="message">
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
            messageData: [],
            collapsed: false,
            channel: null,
            channelObj: Object,
            conversationId : this.$page.props.active_support?.id
        }
    },
    created: function () {
        this.moment = moment;
        this.initialize()
    },
    mounted() {
        this.initChannel()
    },
    methods: {
        async initialize(){
            await axios.get(route('get_support_active_conversation'))
                .then(res => {
                    let data = res.data
                    this.channel = "support."+data.id
                    this.conversationId = data.id
                    this.messageData = data.message
                })
        },
        initChannel(){
            this.channelObj = window.Echo.private("support."+this.conversationId)
                .listen('SupportEvent', (e) => {
                    this.messageData.push(e.conversation)
                })
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
        }
    },
    computed(){
        console.log(this.channel)
    }
}
</script>
