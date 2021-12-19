<template>
    <Head>
        <title>{{mail.subject}} || Diploma IsDB-BISEW</title>
    </Head>
    <Authenticated @load="mailDetails">
        <template #header>
            <PageHeader>
                {{mail.subject}}
            </PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <Back :back-url="route('mail.inbox')"/>
                    <h4>From- <small class="text-gray-700">{{ mail.from }}</small></h4>
                    <h6>@- <small class="text-gray-700">{{ mail.received_at }}</small></h6>
                </div>
                <div class="card-body" ref="mailBody">
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Back from "@/Shared/Back";
export default {
    name: "Details",
    props: ['mail'],
    components: {Back, PageHeader, Authenticated},
    data() {
        return {
            mail_body: this.mail.html_body
        }
    },
    mounted() {
        this.mailDetails()
    },
    methods: {
        mailDetails(){
            const html = this.$refs.mailBody
            html.innerHTML = this.mail_body
            let images = html.getElementsByTagName('img')
            for(let i=0; i < images.length; i++){
                let oldSrc = images[i]?.src?.split(":")[1];
                if(images[i]?.src?.split(":")[0] !== "cid") continue
                let newSrc = this.mail.attachments.filter((value, key) => {
                    return value?.content.search(oldSrc) !== -1
                })
                images[i].src = '/'+newSrc[0]?.content
            }
            return html.innerHTML
        }
    }
}
</script>

<style scoped>

</style>
