<template>
    <app-layout>
        <template #header>
            <h2>Add New Session</h2>
        </template>

        <div class="container">
            <Form :dbValue="dbValue" :errors="errors" :submitForm="submit" :createForm="true"/>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import Form from "./Form";
export default {
    name: "add",
    components: {Form, AppLayout},
    props: ['data', 'flash'],
    data(){
        return {
            dbValue: this.data,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/madrasa', formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
