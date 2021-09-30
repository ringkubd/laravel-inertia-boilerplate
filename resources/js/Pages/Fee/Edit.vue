<template>
    <Head>
        <title>Edit Fee</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Add New Fee</page-header>
        </template>

        <div class="container-fluid">
            <Form :sessions="sessions"  :can="can" :trades="trades" :dbValue="fee" :FeeTypes="fee_types" :errors="errors" :submitForm="submit" :createForm="false"/>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import Form from "./Form";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "edit",
    components: {PageHeader, Form, AppLayout},
    props: ['sessions', 'trades', 'fee_types', 'can', 'flash', 'fee', 'errors'],
    data(){
        return {
            dbValue: this.fee,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.put(route('fee.update', this.fee.id), formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
