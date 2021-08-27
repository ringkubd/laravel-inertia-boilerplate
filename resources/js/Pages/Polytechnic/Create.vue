<template>
    <app-layout>
        <template #header>
            <page-header>Add New Polytechnic</page-header>
        </template>

        <div class="container-fluid">
            <Form :dbValue="dbValue" :errors="errors" :submitForm="submit" :createForm="true"/>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import Form from "./Form";
import PageHeader from "@/Shared/PageHeader"
export default {
    name: "add",
    components: {Form, AppLayout, PageHeader},
    props: ['data', 'flash'],
    data(){
        return {
            dbValue: this.data,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/polytechnic', formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
