<template>
    <Head>
        <title>Your page title</title>
        <meta name="description" content="Your page description">
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Edit Student {{student.name}}</page-header>
        </template>
        <div class="container-fluid">
            <Form :errors="errors"
                  :madrasa="madrasa"
                  :user="user"
                  :student="student"
                  :createForm="false"
                  :postForm="submitForm"
                  :selected_trade="selected_trade"
                  :selected_session="selected_session"
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
    props: ['student', 'selected_trade', 'selected_session', 'academic_session', 'trade_madrasa', 'trade_polytechnic', 'banks', 'selected_bank', 'user', 'polytechnic', 'madrasa', 'errors', 'classes'],
    components: {PageHeader, Back, Form, Authenticated},
    methods: {
        submitForm(formData){
            formData.post(route('madrasa.student.update',this.student.id))
        }
    }
}
</script>

<style scoped>

</style>
