<template>
    <Head>
        <title>Madrasah Result Update</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Add New Result</page-header>
        </template>

        <div class="container-fluid">
            <Form :dbValue="result" :students="students" :submitForm="submit" :errors="errors" :createForm="true"/>
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
    props: ['result', 'flash', 'students', 'errors'],
    data(){
        return {

        };
    },
    methods: {
        submit(formData){
            this.$inertia.post(route('madrasa.result.store'), formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
