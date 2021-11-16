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
                                <li class="border-blue-100 border pl-1.5 mb-1.5 font-bold bg-blend-color bg-green-200 shadow cursor-pointer" v-for="(sup, index) in support" :conversation="sup" @click="changeActiveChat(sup, activeConversation)" key="index">
                                    {{sup.creator.name}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8" @drop="onImageDrop">
                    <div class="card" style="height: 75vh!important;">
                        <div class="card-header">
                            <h2>{{activeConversation?.creator?.name}}</h2>
                        </div>
                        <div class="card-body overflow-scroll" ref="messageContainer" style="height: 58vh!important;">
                            <ul class="p-0.5">
                                <li @focus="seenMessage" class="border-blue-100 border pl-1.5 mb-1.5 shadow cursor-pointer" v-for="(mes, index) in messageData" key="index">
                                    <div class="row" @contextmenu="contextMenu" :messageId="mes.id">
                                        <div  class="col-3" v-if="activeConversation.creator.id === mes.sender.id">
                                             <span class="font-bold border-b">
                                                {{mes.sender.name}}
                                                    <small class="font-normal font-thin italic" style="font-size: 0.5rem">{{moment(mes.created_at).fromNow()}}</small>
                                            </span>
                                        </div>
                                        <div class="col-9 bg-blend-color bg-green-200">
                                            <img v-if="String(mes.attachment_type).search('image') !== -1" :src="mes.attachment" class="img-thumbnail image w-1/2">
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
        <CmpContextMenu :display="showContextMenu" ref="menu">
            <template #default>
                <div class="block">
                    <div class="bg-white w-36 border border-gray-300 rounded-lg flex flex-col text-sm text-gray-500 shadow-lg">
                        <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                            <div class="text-gray-900 inline-block">
                                <jet-button type="submit">
                                    <font-awesome-icon
                                        icon="trash"
                                        size="md"
                                        rotation="rotate"
                                        class="text-danger"
                                    ></font-awesome-icon>
                                    Delete
                                </jet-button>
                            </div>
                        </div>
                        <div class="flex hover:bg-gray-100 py-1 px-2 rounded">
                            <div class="text-gray-900">
                                <font-awesome-icon
                                    icon="copy"
                                    size="md"
                                    rotation="rotate"
                                    class="text-info"
                                ></font-awesome-icon>
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
import moment from "moment";
import CmpContextMenu from "@/Components/Context-menu"
import NavLink from "@/Components/NavLink";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faCopy } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faCopy);
export default {
    props: ['support', 'activeConversation'],
    components: {
        NavLink,
        Authenticated,
        CmpContextMenu
    },
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
            showContextMenu: false,
            clickedMessageId: null,
            deleteUrl: "#"
        }
    },
    mounted() {
        this.scrollToBottom()
        this.messageData = this.activeConversation?.message
        let app = this.channel
        let audio = new Audio('beep.mp3')
        app.listen('SupportEvent', (e) => {
            this.messageData.unshift(e.conversation)
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
        //this.channel = window.Echo.private(`support.`+this.activeConversation?.id);
        this.onlineChannel
            .joining(user => {
                this.onlineUser.unshift(user)
            })
            .listen('SupportOnlineEvent',user => {
                this.onlineUser.unshift(user.user)
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
        changeActiveChat(conversation, oldConversation){
            if(conversation.id !== oldConversation.id){
                window.Echo.leave(`support.${oldConversation.id}`)
            }
            this.channel =  window.Echo.private(`support.`+ conversation?.id);
            this.scrollToBottom()
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
        },
        onImageDrop(e){
            e.preventDefault()
            let files = [...e.dataTransfer.files]
            if (files.length > 0) {
                let attachment = [];
                let formData = new FormData();
                formData.append('attachment', files[0]);
                formData.append('message', this.message)
                formData.append('conversation_id', this.activeConversation.id)

                axios.post(route('support.store'), formData).then(res => {
                    this.messageData.push(res.data.conversation)
                    this.scrollToBottom()
                })
                this.message = "";
            }
        },
        contextMenu(e){
            e.preventDefault()
            this.$refs.menu.open(e);
            this.clickedMessageId = e.currentTarget.getAttribute('messageid')
        },
        onDelete(e){
            if(this.clickedMessageId !== null){

            }
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
        },
        deleteUrl(){
            return this.deleteUrl = route('delete_message', this.clickedMessageId)
        }
    },
    updated(){
        this.channel = window.Echo.private(`support.`+this.activeConversation.id);
    }
}
</script>
