<template>
    <authenticated>
        <template #header>
            <page-header>Edit Trade</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card flex h-screen justify-center mt-5">
                    <div class="card-header">
                        <back :back-url="route('trade.index')"></back>
                    </div>
                    <div class="card-body">
                        <Form :user="user" :errors="errors" :trade="trade" :submitForm="updateForm" form-type="update"></Form>
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
    props: ['user', 'errors', 'trade'],
    components: {Back, Authenticated, Form},
    methods: {
        updateForm(formData){
            this.$inertia.put(route('trade.update', this.trade.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
