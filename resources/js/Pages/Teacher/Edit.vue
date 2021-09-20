<template>
    <Head>
        <title>Edit teacher information | Four Year Diploma</title>
        <meta name="description" content="Your page description">
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Edit Teacher {{teacher.name}}</page-header>
        </template>
        <div class="container-fluid">
            <Form :errors="errors"
                  :madrasa="madrasa"
                  :user="user"
                  :banks="banks"
                  :selected_bank="selected_bank"
                  :teacher="teacher"
                  :createForm="false"
                  :postForm="submitForm"
                  :selected_trade="selected_trade"
                  :designation="designation"
                  :trades="trades">
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
    props: ['teacher', 'selected_trade', 'trades', 'banks', 'selected_bank', 'user', 'madrasa', 'errors', 'designation'],
    components: {PageHeader, Back, Form, Authenticated},
    methods: {
        submitForm(formData){
            formData.post(route('teacher.update',this.teacher.id))
        }
    }
}
</script>

<style scoped>

</style>
