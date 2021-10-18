<template>
    <authenticated>
        <template #header>
            <PageHeader>Add a Page</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-5 min-vh-100">
                    <div class="card-header">
                        <back :back-url="route('page.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :formContent="post" :categories="categories" :tags="tags" :submitForm="storeForm" form-type="store"></Form>
                    </div>
                </div>
            </div>
        </template>
    </authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Back from "@/Shared/Back";
import Form from "./Form"
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "Create",
    props: ['user', 'errors', 'post', 'categories', 'tags'],
    components: {PageHeader, Back, Authenticated, Form},
    methods: {
        storeForm(formData){
            this.$inertia.post(route('page.store'), formData)
        }
    }
}
</script>

<style scoped>

</style>
