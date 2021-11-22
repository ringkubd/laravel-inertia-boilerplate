<template>
    <Head>
        <title>Support Management | IsDB-BISEW</title>
    </Head>
    <Authenticated>
        <div class="container">
            <div class="row h-screen">
                <div class="col-md-4">
                    <div class="card" style="height: 85vh!important;">
                        <div class="card-header">
                            <h4>Active Request List</h4>
                        </div>
                        <div class="card-body chat overflow-scroll" style="height: 58vh!important;">
                            <ul class="p-0.5 chat">
                                <li class="border-blue-100 border pl-1.5 mb-1.5 font-bold bg-blend-color hover:bg-green-200 shadow cursor-pointer" :refs="sup.id" :class="activeConversation?.creator?.id === sup?.creator?.id ? 'chat-active': ''" v-for="(sup, index) in support" :conversation="sup" @click="changeActiveChat(sup, activeConversation)" key="index">
                                    {{sup?.creator?.name}} <span class="border-red-500 bg-green-200 animate-bounce border-2 rounded-full animate-spin w-10">{{supportIdList[sup.id]}}</span>
                                    <span class="font-extralight text-green-800" v-if="isOnline(sup?.creator)">
                                       online
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8" @drop="onImageDrop">
                    <div class="card" style="height: 85vh!important;">
                        <div class="card-header">
                            <h2>{{activeConversation?.creator?.name}}</h2>
                        </div>
                        <div class="card-body overflow-scroll" ref="messageContainer" style="height: 58vh!important;">
                            <ul class="p-0.5">
                                <li @focus="seenMessage" class="border-blue-100 border pl-1.5 mb-1.5 shadow cursor-pointer" v-for="(mes, index) in messageData" key="index">
                                    <div class="row" @contextmenu="contextMenu" :messageId="mes.id">
                                        <div class="col-3 table-cell align-middle" v-if="activeConversation.creator.id === mes.sender.id">
                                             <span class="font-bold border-b table-cell align-middle">
                                                {{mes.sender.name}}
                                                    <small class="font-normal font-thin italic" style="font-size: 0.5rem">{{moment(mes.created_at).fromNow()}}</small>
                                            </span>
                                        </div>
                                        <div class="col-9 bg-blend-color py-1.5">
                                            <img v-if="mes.message === '@like@'" src="/like.png" class="image w-1/12 flex content-center">
                                            <span v-else>{{mes.message}}</span>
                                            <vue-picture-swipe v-if="String(mes.attachment_type).search('image') !== -1" :items="imageItems(mes.attachment)" />
                                            <a v-else-if="mes.attachment_type !== null" :href="mes.attachment" target="_blank">
                                                <font-awesome-icon
                                                    icon="paperclip"
                                                    size="md"
                                                    rotation="rotate"
                                                    class="text-info"
                                                ></font-awesome-icon>
                                                Attachment
                                            </a>
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
                            <div class="row">
                                <div class="col-10">
                                    <form action="" @submit.prevent="sendMessage">
                                        <div class="form-group">
                                            <textarea class="form-control h-20" @keydown="isTyping" v-model="message"> </textarea>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div class="bg-white">
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
                                        <input type="submit" class="btn btn-success" value="Send">
                                    </form>
                                </div>
                                <div class="col-2">
                                    <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-black py-2 px-4 border border-blue-400 border-2 hover:border-transparent rounded" @click="likeButton">
                                        <font-awesome-icon
                                            icon="thumbs-up"
                                            size="lg"
                                            rotation="rotate"
                                            class="text-success"
                                        ></font-awesome-icon>
                                    </button>
                                    <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-black py-2 px-4 border border-blue-400 border-2 hover:border-transparent rounded" @click="doneConversation">
                                        <font-awesome-icon
                                            icon="check"
                                            size="lg"
                                            rotation="rotate"
                                            class="text-info"
                                        ></font-awesome-icon>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CmpContextMenu :display="showContextMenu" ref="menu">
            <template #default>
                <div class="block">
                    <div class="bg-gray-200 w-36 border border-gray-300 rounded-lg flex flex-col text-sm text-gray-500">
                        <div class="flex hover:bg-red-300 my-2 py-2 px-2 rounded">
                            <div class="text-gray-900 inline-block shadow-lg">
                                <jet-button type="submit" @click="deleteMessage">
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
                        <div class="flex hover:bg-gray-100 my-2 py-2 px-2 rounded">
                            <div class="text-gray-900" @click="copyMessage">
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
import { faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck);
import { useConfirm } from 'v3confirm'

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
            onlineSupport: [],
            typing: false,
            typingUser: "",
            typingText: "",
            showContextMenu: false,
            clickedMessageId: null,
            deleteUrl: "#",
            fileName: "Attach you files here",
            online: this.$store.state.onlineFriends,
            confirm: Object,
            allUserConv: [],
            supportIdList: [],
            allTyping: []
        }
    },
    mounted() {
        this.confirm = useConfirm()
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
        for (let i = 0; i < this.support.length; i++){
            let conversation = this.support[i]
            this.supportIdList[conversation.id] = 0
            window.Echo.private(`support.`+ conversation.id).listen('SupportEvent', (e) => {
                this.allUserConv.push(e.conversation)
            }).listenForWhisper('typing', (e) => {
                this.allTyping.push(e.typingUser.id)
                let _this = this
                setTimeout(function() {
                    _this.allTyping.splice(e.typingUser.id, 1)
                }, 4500);
            });
        }
    },
    created: function () {
        this.moment = moment;
        //this.channel = window.Echo.private(`support.`+this.activeConversation?.id);
        this.onlineChannel
            .joining(user => {
                this.onlineSupport.pushIfNotExist(user, function (e){
                    return e.id === user.id
                })
            })
            .listen('SupportOnlineEvent',user => {
                this.onlineSupport.push(user.user)
            })
    },
    methods: {
        scrollToBottom(){
            const el = this.$refs.messageContainer
            el.scrollTop = el.scrollHeight;
        },
        likeButton(){
            this.message = "@like@"
            this.sendMessage()
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
            formData.append('conversation_id', this.activeConversation.id)

            axios.post(route('support.store'), formData).then(res => {
                this.messageData.push(res.data.conversation)
                this.scrollToBottom()
            })
            this.message = "";
            this.fileName = "Attach you files here"
            this.$refs.attachment.value = null
        },
        changeActiveChat(conversation, oldConversation){
            if(conversation.id !== oldConversation.id){
                window.Echo.leave(`support.${oldConversation.id}`)
            }
            this.channel =  window.Echo.private(`support.`+ conversation?.id);
            this.scrollToBottom()
            this.$inertia.visit(route('support.index')+`?conversation_id=${conversation.id}`)
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
        },
        onFileSelect(){
            this.fileName = this.$refs.attachment?.files.length > 0 ? this.$refs.attachment.files[0].name : "Attach you files here"
        },
        isOnline(obj) {
            let online = this.online
            for (var i = 0; i < online.length; i++) {
                if (online[i].id === obj.id) { // modify whatever property you need
                    return true;
                }
            }
            return false
        },
        doneConversation(e){
            this.confirm.show('Are you sure?').then((ok) => {
                if (ok){
                    axios.put(route('support.update', this.activeConversation.id))
                        .then(res => {
                            this.$inertia.visit(route('support.index'))
                        })
                }else{
                    alert('Users not deleted')
                }
            })
        },
        deleteMessage(){
            alert(this.clickedMessageId)
        },
        copyMessage(){
            let _this = this
            let message = this.messageData.filter((mes, index) => {
                return mes.id === parseInt(_this.clickedMessageId)
            })
            console.log(message)
        },
        imageItems(images){
            let items = []
            var item = {
                src: images,
                thumbnail: images,
                w: 1200,
                h: 900,
                title: 'Will be used for caption'
            }
            items.push(item)
            return items;
        },
        allNewMessage(suppid){
            let newMsg = this.allUserConv.filter((conv, index) => {
                return suppid === conv.id
            })
            return newMsg.length
        },
        count_duplicate(a){
            let counts = {}
            for(let i =0; i < a.length; i++){
                let index = a[i].support_conversation_id
                if (counts[index] && index !== undefined){
                    counts[index] += 1
                } else if(index !== undefined) {
                    counts[index] = 1
                }
            }
            console.log(counts)

           return counts
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
        },
        online(){
            return this.online = this.$store.state.onlineFriends
        },
        supportIdList(){
            return this.count_duplicate(this.allUserConv)
        },
        allTyping(){
            return this.allTyping;
        }

    },
    updated(){
        this.channel = window.Echo.private(`support.`+this.activeConversation?.id);
    }
}
</script>
<style>
.pswp__img--placeholder--blank{
    display: none !important;
}
.gallery-thumbnail img {
    object-fit: contain !important;
    max-width: 15rem!important;
    margin: 0!important;
    margin-bottom: 0!important;
    margin-top: 0!important;
}
.gallery-thumbnail{
    display: flex!important;
    margin: 0 0.2rem 0.09rem 0!important;
}
.pswp__bg {
    background-color: #fff !important;
}
.Pswp_bg image-zoom-background {
    background-color: #fff !important;
}
.my-gallery{
    display: inline-flex!important;
}
</style>
