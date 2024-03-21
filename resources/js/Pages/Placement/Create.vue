<template>
    <Head>
        <title>Add New Placement</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Add New Placement</page-header>
        </template>

        <div class="container-fluid">
            <Form
                :dbValue="dbValue"
                :submitForm="submit"
                :createForm="true"
                :academic_sessions="academic_sessions"
                :errors="$page.props.errors"
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
            this.$inertia.post('/placement', formData)
        }
    },
    mounted() {
    },
    updated() {
    }
}
</script>

<style scoped>

</style>
