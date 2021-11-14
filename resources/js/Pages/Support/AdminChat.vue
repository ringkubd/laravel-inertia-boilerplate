<template>
    <Authenticated>
        <div class="container">
            <div class="row h-screen">
                <div class="col-md-4">
                    <div class="card" style="height: 75vh!important;">
                        <div class="card-header">
                            <h4>Active Request List</h4>
                        </div>
                        <div class="card-body overflow-scroll" style="height: 58vh!important;">
                            <ul class="p-0.5">
                                <li class="border-blue-100 border pl-1.5 mb-1.5 font-bold bg-blend-color bg-green-200 shadow cursor-pointer" v-for="(sup, index) in support" :conversation="sup" @click="changeActiveChat(sup)" key="index">
                                    {{sup.creator.name}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card" style="height: 75vh!important;">
                        <div class="card-header">
                            <h2>{{activeConversation?.creator?.name}}</h2>
                        </div>
                        <div class="card-body overflow-scroll" ref="messageContainer" style="height: 58vh!important;">
                            <ul class="p-0.5">
                                <li @focus="seenMessage" :messageId="mes.id" class="border-blue-100 border pl-1.5 mb-1.5 shadow cursor-pointer" v-for="(mes, index) in messageData" key="index">
                                    <div class="row">
                                        <div class="col-3" v-if="activeConversation.creator.id === mes.sender.id">
                                             <span class="font-bold border-b">
                                                {{mes.sender.name}}
                                                    <small class="font-normal font-thin italic" style="font-size: 0.5rem">{{moment(mes.created_at).fromNow()}}</small>
                                            </span>
                                        </div>
                                        <div class="col-9 bg-blend-color bg-green-200">
                                            {{mes.message}}
                                        </div>

                                        <div class="col-3" v-if="activeConversation.creator.id !== mes.sender.id">
                                             <span class="font-bold border-b">
                                                {{mes.sender.name}}
                                                    <small class="font-normal font-thin italic" style="font-size: 0.5rem">{{moment(mes.created_at).fromNow()}}</small>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer static bottom-0" style="width: 100%">
                            <span v-if="typing"><b>{{typingUser.name}} <i>typing..</i>:</b> {{typingText}}</span>
                            <form action="" @submit.prevent="sendMessage">
                                <input type="text" class="form-control h-20" @keydown="isTyping" v-model="message">
                                <input type="submit" class="btn btn-success" value="Send">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
<script>
import Authenticated from "@/Layouts/Authenticated";
import moment from "moment";
export default {
    props: ['support', 'activeConversation'],
    components: {Authenticated},
    data() {
        return {
            modal: {
                show: false,
            },
            message: '',
            messageData: [],
            onlineUser: [],
            typing: false,
            typingUser: "",
            typingText: "",
        }
    },
    mounted() {
        this.scrollToBottom()
        this.messageData = this.activeConversation?.message
        let app = this.channel
        let audio = new Audio('beep.mp3')
        app.listen('SupportEvent', (e) => {
            this.messageData.push(e.conversation)
            audio.load()
            audio.play()
            this.typing = false
        }).listenForWhisper('typing', (e) => {
            this.typingUser = e.typingUser;
            this.typing = e.typing;
            this.typingText = e.typingText;
            let _this = this
            // remove is typing indicator after 0.9s
            setTimeout(function() {
                _this.typing = false
            }, 4500);
        });
    },
    created: function () {
        this.moment = moment;
        this.channel = window.Echo.private(`support.`+this.activeConversation?.id);
        this.onlineChannel
            .here(users => {
                this.onlineUser = [...new Set([...this.onlineUser, ...users])]
            })
            .joining(user => {
                let users = [user]
                this.onlineUser = [...new Set([...this.onlineUser, ...users])]
            })
            .listen('SupportOnlineEvent',user => {
            //console.log(user.user)
        })
    },
    methods: {
        scrollToBottom(){
            const el = this.$refs.messageContainer
            el.scrollTop = el.scrollHeight;
        },
        sendMessage() {
            if(this.message.replace(/^\s+|\s+$/g, "") === ""){
                return;
            }
            axios.post(route('support.store'), {
                message: this.message,
                conversation_id: this.activeConversation.id
            }).then(res => {
                this.messageData.push(res.data.conversation)
                this.scrollToBottom()
            })
            this.message = "";
        },
        changeActiveChat(conversation){
            this.$inertia.replace(route('support.index')+`?conversation_id=${conversation.id}`)
        },
        isTyping(){
            let _this = this
            setTimeout(function() {
                _this.channel.whisper('typing', {
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
    computed: {
        messageData(){
            return this.messageData = this.activeConversation?.message
        },
        channel(){
            return this.channel = window.Echo.private(`support.`+this.activeConversation.id);
        },
        onlineChannel(){
            return this.onlineChannel = window.Echo.join('support')
        }
    },
    updated(){
        this.channel = window.Echo.private(`support.`+this.activeConversation.id);
    }
}
</script>
