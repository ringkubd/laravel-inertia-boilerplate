<template>
    <authenticated>
        <template #header>
            <page-header>Add Trade</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-1">
                    <div class="card-header">
                        <back :back-url="route('trade.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :trade="trade" :submitForm="updateForm" form-type="store"></Form>
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
    props: ['user', 'errors', 'trade'],
    components: {PageHeader, Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.post(route('trade.store'), formData)
        }
    }
}
</script>

<style scoped>

</style>
