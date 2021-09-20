<template>
    <authenticated>
        <template #header>
            <page-header>
                Add Teacher
            </page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <Form :errors="errors"
                      :madrasa="madrasa"
                      :user="user"
                      :banks="banks"
                      :selected_bank="selected_bank"
                      :teacher="teacher"
                      :createForm="true"
                      :postForm="submitForm"
                      :selected_trade="selected_trade"
                      :designation="designation"
                      :trades="trades">
                </Form>
            </div>
        </template>
    </authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Form from "@/Pages/Teacher/Form";
export default {
    name: "Create",
    props: ['teacher', 'selected_trade', 'trades', 'banks', 'selected_bank', 'user', 'madrasa', 'errors', 'designation'],
    components: {Form, PageHeader, Authenticated},
    data(){

    },
    methods: {
        submitForm(formData){
            formData.post(route('teacher.store'))
        }
    }
}
</script>

<style scoped>

</style>
