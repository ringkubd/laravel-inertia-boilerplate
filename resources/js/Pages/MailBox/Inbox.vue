<template>
    <Head>
        <title>Personal Mail Inbox</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Inbox</PageHeader>
        </template>
        <div class="flex">
            <div class="min-h-screen w-full">
                <div  class="card min-h-screen">
                    <div class="card-header"></div>
                    <div class="card-body chat overflow-scroll" style="height: 58vh!important;">
                        <ul class="p-0.5 chat">
                            <li class="border-blue-100 border pl-1.5 mb-1.5 font-bold bg-blend-color hover:bg-green-200 shadow cursor-pointer" :key="index" v-for="(mail, index) in mails.data" @click="mailDetailsF(mail)">
                                <Link class="no-underline text-black block" :href="route('mail.details', mail.id)">
                                <span class="font-extralight text-green-800" v-if="mail.seen_at === null">
                                    <font-awesome-icon
                                        icon="dot-circle"
                                        size="sm"
                                        rotation="rotate"
                                        class="text-green-600"
                                    ></font-awesome-icon>
                                </span>
                                    {{mail?.sender}}
                                    <br>
                                    <i class="text-xs text-gra">{{truncte(mail?.subject)}}</i>
                                    <sup class="animate-bounce rounded-full h-5 w-5 bg-green-200">

                                    </sup>
                                    <span class="rounded-full animate-pulse overflow-scroll w-5 font-thin font-extralight text-blue-700" style="font-size: .7em">

                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <Paginator :paginator="mails" />
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck, faDotCircle } from "@fortawesome/free-solid-svg-icons";
import {truncate} from "@/Helpers/auth-header";
import Paginator from "@/Components/Paginator";
import PageHeader from "@/Shared/PageHeader";
library.add(faPen, faTrash, faCopy, faPaperclip, faThumbsUp, faCheck, faDotCircle);
export default {
    name: "Inbox",
    props: ['mails', 'menu_permission', 'students'],
    components: {PageHeader, Paginator, Authenticated},
    data(){
        return{
            mailDetails: [],
            channel: {},
            mails: this.mails
        }
    },
    created(){
        if(this.menu_permission.super_admin){
            let _this =  this
            this.channel = window.Echo.private('admin_mail')
                .listen('AdminMailEvent', function (e) {
                    _this.mails.data.unshift(e.message)
                })
        }else if (this.menu_permission.student){
            let _this =  this
            this.channel = window.Echo.private(this.$page.props.user.email)
                .listen('MailReceivedEvent', function (e) {
                    _this.mails.data.unshift(e.message)
                })
        }
    },
    methods: {
        truncte(str, n){
            return truncate(str, n);
        },
        mailDetailsF(mail){

            const html =  this.createElementFromHTML(mail.html_body)
            let images = html.getElementsByTagName('img')
            for(let i=0; i < images.length; i++){
                let oldSrc = images[i]?.src?.split(":")[1];

                if(images[i]?.src?.split(":")[0] !== "cid") continue

                let newSrc = mail.attachments.filter((value, key) => {
                    return value?.content.search(oldSrc) !== -1
                })
                images[i].src = '/'+newSrc[0]?.content
            }
            mail.html_body = html.innerHTML

            this.mailDetails = mail
        },
        createElementFromHTML(htmlString) {
            var div = document.createElement('div');
            div.innerHTML = htmlString.trim();

            // Change this to div.childNodes to support multiple top-level nodes
            return div;
        }
    }
}
</script>

<style scoped>

</style>
