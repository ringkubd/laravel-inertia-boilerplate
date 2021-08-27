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
export default {
    name: "Create",
    props: ['user', 'errors', 'post', 'categories', 'tags'],
    components: {Back, Authenticated, Form},
    methods: {
        storeForm(formData){
            this.$inertia.post(route('page.store'), formData)
        }
    }
}
</script>

<style scoped>

</style>
