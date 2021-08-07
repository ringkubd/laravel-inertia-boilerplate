<template>
    <authenticated>
        <template #header>
            <h2>Add a Post</h2>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <back :back-url="route('post.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors"></Form>
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
    props: ['user', 'errors'],
    components: {Back, Authenticated, Form}
}
</script>

<style scoped>

</style>
