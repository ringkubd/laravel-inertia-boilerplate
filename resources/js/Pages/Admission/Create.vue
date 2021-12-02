<template>
    <Head>
        <title>Add admission</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Admission Management</page-header>
        </template>
        <div class="container-fluid min-h-screen">
           <div class="card">
               <div class="card-header">
                   <Back :back-url="route('admission.index')"/>
               </div>
               <div class="card-body">
                   <Form :can="can" :students="students" :errors="this.$page.props.errors" :create-form="true" :trades="trades" :sessions="sessions" :polytechnic="polytechnic" :submitForm="submitForm" />
               </div>
           </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Form from './Form'
import Back from "@/Shared/Back";
export default {
    name: "Create",
    props: ['can', 'students', 'trades', 'sessions', 'polytechnic'],
    components: {
        Back,
        Authenticated,
        Form
    },
    mounted(){
        console.log(this.trades)
    },
    methods:{
        submitForm(formData){
            formData.post(route('admission.store'))
        }
    }
}
</script>

<style scoped>

</style>
