<template>
    <authenticated>
        <template #header>
            <h2>Edit Category</h2>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <back :back-url="route('category.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :formContent="category" :parent="parent" :submitForm="updateForm" form-type="update"></Form>
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
export default {
    name: "Edit",
    props: ['user', 'errors', 'category', 'parent'],
    components: {Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.put(route('category.update', this.category.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
