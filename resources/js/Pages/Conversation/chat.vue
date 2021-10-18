<template>
    <Head>
        <title>Private Conversation</title>
    </Head>
    <breeze-authenticated-layout>
        <template #header>
            <div class="row">
                <div class="col-6">
                    <PageHeader>Conversation</PageHeader>
                </div>
                <div class="col-6">
                    <PageHeader>
                        {{secondPerson}}
                    </PageHeader>
                </div>
            </div>
        </template>
        <template v-slot:default="slotProps">
            <div class="flex h-screen antialiased text-gray-800">
                <div class="flex flex-row h-full w-full overflow-x-hidden">
                    <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
                        <div class="flex flex-row items-center justify-center h-12 w-full">
                            <div
                                class="flex items-center justify-center rounded-2xl text-indigo-700 bg-indigo-100 h-10 w-10"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-2 font-bold text-2xl">QuickChat</div>
                        </div>
                        <div
                            class="flex flex-col items-center bg-indigo-100 border border-gray-200 mt-4 w-full py-6 px-4 rounded-lg"
                        >
                            <div class="h-20 w-20 rounded-full border overflow-hidden">
                                <img
                                    src="https://avatars3.githubusercontent.com/u/2763884?s=128"
                                    alt="Avatar"
                                    class="h-full w-full"
                                />
                            </div>
                            <div class="text-sm font-semibold mt-2">{{user.name}}.</div>
                            <div class="text-xs text-gray-500">{{user.title}}</div>
                            <div class="flex flex-row items-center mt-3">
                                <div
                                    class="flex flex-col justify-center h-4 w-8 bg-indigo-500 rounded-full"
                                >
                                    <div class="h-3 w-3 bg-white rounded-full self-end mr-1"></div>
                                </div>
                                <div class="leading-none ml-1 text-xs">Active</div>
                            </div>
                        </div>
                        <div class="flex flex-col mt-8">
                            <div class="flex flex-row items-center justify-between text-xs">
                                <span class="font-bold">Online</span>
                                <span
                                    class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                                >{{slotProps.onlineFriends.length}}</span
                                >
                            </div>
                            <div class="flex flex-col space-y-1 mt-4 -mx-2 h-48 overflow-y-auto" v-if="slotProps.onlineFriends.length > 0">
                                <button
                                    :class="onl.id === $store.state.activeChatTarget ? activeUserClass : inActiveClass"   v-for="onl in online"
                                    @click="activeUserMessage(onl.id)"
                                >
                                    <div
                                        class="flex items-center justify-center h-8 w-8 bg-gray-200 rounded-full"
                                    >
                                        {{firstLetter(onl.name)}}
                                    </div>
                                    <div class="ml-2 text-sm font-semibold">{{onl.name}}</div>
                                    <div class="flex items-center justify-center ml-auto text-xs text-white bg-red-500 h-4 w-4 rounded leading-none" v-if="onl.conversation.length > 0">
                                        {{onl.conversation[0].messages.length}}
                                    </div>
                                </button>
                            </div>


                            <div class="flex flex-row items-center justify-between text-xs mt-6">
                                <span class="font-bold">Offline</span>
                                <span
                                    class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                                >{{slotProps.offlineFriends.length}}</span
                                >
                            </div>
                            <div class="flex flex-col space-y-1 mt-4 -mx-2" v-if="slotProps.offlineFriends.length > 0">
                                <button
                                    :class="off.id === $store.state.activeChatTarget ? activeUserClass : inActiveClass"  v-for="off in $page.props.offline"
                                    @click="activeUserMessage(off.id)"
                                >
                                    <div
                                        class="flex items-center justify-center h-8 w-8 bg-gray-200 rounded-full"
                                    >
                                        {{firstLetter(off.name)}}
                                    </div>
                                    <div class="ml-2 text-sm font-semibold">{{off.name}}</div>
                                    <div class="flex items-center justify-center ml-auto text-xs text-white bg-red-500 h-4 w-4 rounded leading-none" v-if="off.conversation.length > 0">
                                        {{off.conversation[0].messages.length}}
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col flex-auto h-full p-6">
                        <div
                            class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
                        >
                            <div class="flex flex-col h-full overflow-x-auto mb-4">
                                <div class="flex flex-col h-full">
                                    <div class="grid grid-cols-12 gap-y-2" v-for="message in $store.state.messages">
                                        <div class="col-start-1 col-end-8 p-3 rounded-lg"  v-if="message.sender === $page.props.user.id">
                                            <div class="flex flex-row items-center">
                                                <div
                                                    class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 flex-shrink-0"
                                                >
                                                    {{$page.props.user.name}}
                                                </div>
                                                <div
                                                    class="relative ml-3 text-sm bg-white py-2 px-4 shadow rounded-xl"
                                                >
                                                    <div> {{message.body}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-start-6 col-end-13 p-3 rounded-lg" v-else>
                                            <div class="flex items-center justify-start flex-row-reverse">
                                                <div
                                                    class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 flex-shrink-0"
                                                >
                                                    {{secondPerson}}
                                                </div>
                                                <div
                                                    class="relative mr-3 text-sm bg-indigo-100 py-2 px-4 shadow rounded-xl"
                                                >
                                                    <div v-contextmenu:contextmenu>{{message.body}}</div>
                                                    <v-contextmenu ref="contextmenu">
                                                        <v-contextmenu-item>Menu Item 1</v-contextmenu-item>
                                                        <v-contextmenu-item>Menu Item 2</v-contextmenu-item>
                                                        <v-contextmenu-item>Menu Item 3</v-contextmenu-item>
                                                    </v-contextmenu>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span v-show="typing" class="help-block" style="font-style: italic;">
                                    @{{ typingUser }} is typing... {{typingText}} .....
                                </span>
                            <div
                                class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4"
                            >

                                <div>
                                    <button
                                        class="flex items-center justify-center text-gray-400 hover:text-gray-600"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-grow ml-4">
                                    <div class="relative w-full">
                                        <input
                                            type="text"
                                            class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                                            v-model="sendForm.body"
                                            @keydown="isTyping"
                                            id="sendMessage"
                                        />
                                        <input-error :message="errors.body"></input-error>
                                        <button
                                            class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button
                                        class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
                                        type="submit"
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
        </template>
    </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import InputError from "@/Components/InputError";
import "v-contextmenu/dist/themes/default.css";
import { directive, Contextmenu, ContextmenuItem  } from "v-contextmenu"
export default {
    name: "chat",
    props: ['online', 'offline', 'user', 'errors', 'conversation'],
    directives: {
        contextmenu: directive,
    },
    components: {
        InputError,
        PageHeader,
        BreezeAuthenticatedLayout,
        [Contextmenu.name]: Contextmenu,
        [ContextmenuItem.name]: ContextmenuItem,
    },
    data() {
        return {
            activeUserClass: "flex flex-row items-center hover:bg-gray-100 rounded-xl p-2 outline-none ring-2 ring-purple-600 border-transparent",
            inActiveClass: "flex flex-row items-center hover:bg-gray-100 rounded-xl p-2",
            sendForm: {
                conversation_id : "",
                sender: this.user.id,
                body: ""
            },
            secondPerson: localStorage.getItem('second_person'),
            typing: false,
            typingUser: "",
            typingText: "",
            otherTyping: false,
            otherTypingUser: "",
            otherTypingText: ""
        }
    },
    mounted(){
        if(window.location.pathname === "/conversation"){
            let firstUser = this.online.length > 0 ? this.online[0].id : this.offline[0].id;
            this.$inertia.replace(route('conversation.show', firstUser))
        }
        if (this.$store.state.activeChatTarget === null){
            let activeUserFromLocal = localStorage.getItem('activeChatTarget')
            if (activeUserFromLocal === undefined || activeUserFromLocal === null){
                if(this.online.length > 0){
                    this.$store.dispatch('setActiveChatTarget',  this.online[0].id)
                    this.activeUserMessage(this.$store.state.activeChatTarget)
                } else if(this.offline.length > 0){
                    this.$store.dispatch('setActiveChatTarget',  this.offline[0].id)
                    this.activeUserMessage(this.$store.state.activeChatTarget)
                }
            }else{
                this.$store.dispatch('setActiveChatTarget',  activeUserFromLocal)
                this.activeUserMessage(this.$store.state.activeChatTarget)
            }

        }else{
            this.activeUserMessage(this.$store.state.activeChatTarget)
        }
        let __this = this;
        document.querySelector('#sendMessage').addEventListener('keypress', function(e){
            if(e.key === 'Enter'){
                __this.sendMessage()
            }
        });
    },
    methods: {
        firstLetter(str){
            if (str == "") return;
            return str.charAt(0).toUpperCase()
        },
        messageLength(){

        },
        conversationLength(){
            const offline = this.offline;
            const online = this.online;
        },
        activeUserMessage(user){
            window.Echo.leave('messages.'+this.conversation.id)
            this.$inertia.replace(route('conversation.show', user))

            localStorage.setItem('activeChatTarget', user)
            this.$store.dispatch('setActiveChatTarget', user)
            axios.get(route('get_active_conversation', user))
                .then(response => {
                    let second = response.data.conversation_users.filter((user) => {
                        return user.id !== this.$page.props.user.id
                    })
                    localStorage.setItem('second_person', second[0].name)
                    this.secondPerson = second[0].name
                    this.$store.dispatch('messagesInit', response.data.messages)
                    this.$store.dispatch('setActiveConversation', response.data.id)
                    localStorage.setItem('active_conversation', response.data.id)
                    this.sendForm.conversation_id = response.data.id;
                })
                .catch(error => {
                    console.log(error)
                })
        },
        sendMessage(){
            axios
                .post(route('conversation.store'), this.sendForm)
                .then(response => {
                    this.$store.dispatch('sendMessage', response.data)
                    this.sendForm.body = ""
                })
        },
        receiveMessage(){

        },
        isTyping(){
            let channel = this.channel;
            let _this = this;
            setTimeout(function(){
                channel.whisper('typing', {
                    user: _this.user.name,
                    typing: true,
                    typingText: _this.sendForm.body,
                    conversation_id: _this.conversation.id
                })
            }, 300)
        }
    },
    created() {
        let app = this.channel;
        let _this = this;
        app
            .listen('MessageEvent', (e) => {
                if (this.conversation.id === e.conversation_id){
                    this.$store.dispatch('sendMessage', e.message)
                }
            })
            .listenForWhisper('typing', (e) => {
                if (e.conversation_id == _this.conversation.id){
                    this.typingUser = e.user;
                    this.typing = e.typing;
                    this.typingText = e.typingText;

                    setTimeout(function() {
                        _this.typing = false
                    }, 6000);
                }else{
                    this.otherTypingUser = e.user;
                    this.otherTyping = e.typing;
                    this.otherTypingText = e.typingText;

                    setTimeout(function() {
                        _this.otherTyping = false
                    }, 6000);
                    console.log(e)
                }
            })
    },
    computed: {
        conversionId(){
            return this.sendForm.conversation_id
        },
        channel () {
            if(this.conversation !== undefined){
                return window.Echo.private(`messages.${this.conversation.id}`);
            }
            if(window.location.pathname === "/conversation"){
                let firstUser = this.online.length > 0 ? this.online[0].id : this.offline[0].id;
                this.$inertia.replace(route('conversation.show', firstUser))
            }
        },
        secondPerson(){
            this.secondPerson = localStorage.getItem('second_person')
            return this.secondPerson;
        }
    }

}
</script>

<style scoped>

</style>
