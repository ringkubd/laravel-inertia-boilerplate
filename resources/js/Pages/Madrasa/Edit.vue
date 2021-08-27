<template>
    <app-layout>
        <template #header>
            <h2>Edit Madrasa</h2>
        </template>

        <div class="container-fluid">
            <Form :dbValue="dbValue" :errors="errors" :submitForm="submit" :createForm="false"/>
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
    props: ['data', 'flash'],
    data(){
        return {
            dbValue: this.data,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.put(route('madrasa.update', this.dbValue.id), formData)
        }
    },
    mounted() {
        console.log(this.dbValue)
    }
}
</script>

<style scoped>

</style>
