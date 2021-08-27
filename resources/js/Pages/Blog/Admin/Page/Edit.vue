<template>
    <authenticated>
        <template #header>
            <h2>Add a Page</h2>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
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
export default {
    name: "Edit",
    props: ['user', 'errors', 'post', 'categories', 'tags'],
    components: {Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.put(route('page.update', this.post.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
