<template>
    <authenticated>
        <template #header>
            <page-header>Add a Post</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-5 min-vh-100">
                    <div class="card-header">
                        <back :back-url="route('post.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :formContent="post" :categories="categories" :tags="tags" :submitForm="updateForm" form-type="update"></Form>
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
    name: "Edit",
    props: ['user', 'errors', 'post', 'categories', 'tags'],
    components: {PageHeader, Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.put(route('post.update', this.post.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
