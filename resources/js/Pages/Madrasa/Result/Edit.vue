<template>
    <app-layout>
        <template #header>
            <h2>Edit Madrasa</h2>
        </template>

        <div class="container-fluid">
            <Form :dbValue="result" :students="students" :submitForm="submit" :errors="errors" :createForm="false"/>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import Form from "./Form";
export default {
    name: "edit",
    components: {
        AppLayout,
        Form
    },
    props: ['result', 'flash', 'students', 'errors'],
    data(){
        return {
            dbValue: this.result,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.put(route('madrasa.result.update', this.dbValue.id), formData)
        }
    },
    mounted() {
        console.log(this.dbValue)
    }
}
</script>

<style scoped>

</style>
