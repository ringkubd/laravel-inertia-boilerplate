<template>
    <authenticated>
        <template #header>
            <PageHeader>Add a Category</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-1 min-vh-100">
                    <div class="card-header">
                        <back :back-url="route('category.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :formContent="category" :parent="parent" :submitForm="updateForm" form-type="store"></Form>
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
    props: ['user', 'errors', 'category', 'parent'],
    components: {PageHeader, Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.post(route('category.store'), formData)
        }
    }
}
</script>

<style scoped>

</style>
