<template>
    <authenticated>
        <template #header>
            <page-header>Add a Page</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-1">
                    <div class="card-header">
                        <back :back-url="route('page.index')"></back>
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
            this.$inertia.put(route('page.update', this.post.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
