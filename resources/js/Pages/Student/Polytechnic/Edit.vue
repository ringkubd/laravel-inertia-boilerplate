<template>
    <Head>
        <title>Edit Student Information</title>
        <meta name="description" content="Edit Student Information">
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Edit Student {{student.name}}</page-header>
        </template>
        <div class="container-fluid">
            <Form :errors="errors"
                  :polytechnic="polytechnic"
                  :user="user"
                  :banks="banks"
                  :selected_bank="selected_bank"
                  :student="student"
                  :createForm="false"
                  :postForm="submitForm"
                  :selected_trade="selected_trade"
                  :selected_session="selected_session"
                  :trade_polytechnic="trade_polytechnic"
                  :trade_madrasa="trade_madrasa"
                  :classes="classes"
                  :academic_session="academic_session">
            </Form>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Form from "./Form";
import Back from "@/Shared/Back";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "Edit",
    props: [
        'student', 'selected_trade', 'selected_session', 'academic_session', 'trade_madrasa',
        'trade_polytechnic', 'banks', 'selected_bank', 'user', 'polytechnic', 'madrasa', 'errors',
        'classes'
    ],
    components: {PageHeader, Back, Form, Authenticated},
    methods: {
        submitForm(formData){
            formData.post(route('polytechnic.student.update',this.student.id))
        }
    }
}
</script>

<style scoped>

</style>
