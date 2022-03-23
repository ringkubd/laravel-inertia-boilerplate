<template>
    <Head>
        <title>Add Notice</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Add New Notice</page-header>
        </template>

        <div class="container-fluid">
            <Form :sessions="sessions"  :can="can" :class_rooms="class_rooms" :notice="notice" :submitForm="submit" :createForm="true"/>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import Form from "./Form";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "add",
    components: {PageHeader, Form, AppLayout},
    props: ['sessions', 'class_rooms', 'can', 'flash', 'notice'],
    data(){
        return {
            dbValue: this.notice,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/notice', formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
