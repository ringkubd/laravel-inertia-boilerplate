<template>
    <Head>
        <title>Teacher's Conversation</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Teacher's Conversation</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can">
                        <template #first>
                            <Back :back-url="route('teacher_conversation')" />
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <form @submit.prevent="submitForm">
                        <div class="flex flex-col w-full space-y-5">
                            <div class="flex flex-col w-full">
                                <Label for="to" title="To">To</Label>
                                <Select2
                                    :options="teachers"
                                    v-model="form.to"
                                    id="to"
                                />
                            </div>
                            <div class="flex flex-col w-full">
                                <Label for="subject" title="Subject">Subject</Label>
                                <Input v-model="form.subject" />
                            </div>
                            <div class="flex flex-col w-full">
                                <Label for="message" title="Message">Message</Label>
                                <textarea v-model="form.message" />
                            </div>
                            <Button>Submit</Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
import PageHeader from "@/Shared/PageHeader";
import Back from "@/Shared/Back.vue";
import Form from "@/Pages/Blog/Admin/Post/Form.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/inertia-vue3";
import Button from "@/Shared/Button.vue";
export default {
    name: "Index",
    components: {Button, Input, Label, Form, Back, PageHeader, Paginator, Actions, CardHeader, Authenticated},
    props: ['can', 'teachers'],
    data(){
        return {
            filterParam: {
                trade: null,
                session: null
            },
            form: useForm({
                to : '',
                subject: '',
                message: ''
            })
        }
    },
    methods: {
        search(query){
            this.$inertia.replace(route('teacher.index', {'search': query}))
        },
        submitForm(){
            this.form.post(route('teacher.message.store'))
        }
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
