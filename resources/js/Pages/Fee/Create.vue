<template>
    <Head>
        <title>Add Fee's</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Add New Fee</page-header>
        </template>

        <div class="container-fluid">
            <Form :sessions="sessions"  :can="can" :trades="trades" :dbValue="fee" :FeeTypes="fee_types" :errors="errors" :submitForm="submit" :createForm="true"/>
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
    props: ['sessions', 'trades', 'fee_types', 'can', 'flash', 'fee', 'errors'],
    data(){
        return {
            dbValue: this.fee,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/fee', formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
