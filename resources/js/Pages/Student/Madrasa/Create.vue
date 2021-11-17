<template>
    <Head>
        <title>Student Add Page | IsDB-BISEW</title>
        <meta name="description" content="Your page description">
    </Head>
    <authenticated>
        <template #header>
            <page-header>
                Add Student
            </page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <Form :errors="errors"
                      :polytechnic="polytechnic"
                      :madrasa="madrasa"
                      :user="user"
                      :banks="banks"
                      :selected_bank="selected_bank"
                      :student="student"
                      :createForm="true"
                      :postForm="submitForm"
                      :selected_trade="selected_trade"
                      :selected_session="selected_session"
                      :trade_polytechnic="trade_polytechnic"
                      :trade_madrasa="trade_madrasa"
                      :classes="classes"
                      :academic_session="academic_session">

                </Form>
            </div>
        </template>
    </authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Form from "@/Pages/Student/Madrasa/Form";
export default {
    name: "Create",
    props: ['student', 'selected_trade', 'selected_session', 'academic_session', 'trade_madrasa', 'trade_polytechnic', 'banks', 'selected_bank', 'user', 'polytechnic', 'madrasa', 'errors', 'classes'],
    components: {Form, PageHeader, Authenticated},
    data(){

    },
    methods: {
        submitForm(formData){
            formData.post(route('madrasa.student.store'))
        }
    }
}
</script>

<style scoped>

</style>
