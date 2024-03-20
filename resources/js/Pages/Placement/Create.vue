<template>
    <app-layout>
        <template #header>
            <page-header>Add New Madrasa</page-header>
        </template>

        <div class="container-fluid">
            <Form
                :dbValue="dbValue"
                :errors="errors"
                :submitForm="submit"
                :createForm="true"
                :academic_sessions="academic_sessions"
            />
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
    props: ['academic_sessions','students', 'flash'],
    data(){
        return {
            dbValue: this.data,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/madrasa', formData)
        }
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
