<template>
    <Head>
        <title>Polytechnic's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>
                Polytechnic Management
            </page-header>
        </template>
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('polytechnic.create')" :search-method="search"></CardHeader>
                </div>
                <table class="table table-secondary table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Institute Number</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="polytechnic in data" :key="polytechnic.id">
                        <td>{{polytechnic.name}}</td>
                        <td>{{polytechnic.email}}</td>
                        <td>{{polytechnic.contact_number}}</td>
                        <td>{{polytechnic.address}}</td>
                        <td>{{polytechnic.institution_number}}</td>
                        <td>
                            <Actions :can="can" :delete-url="route('polytechnic.destroy', polytechnic.id)" :edit-url="route('polytechnic.edit', polytechnic.id)"></Actions>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
export default {
    components: {
        Actions,
        CardHeader,
        AppLayout
    },
    props: ['data', 'errors', 'flash', 'can'],
    data(){
        return {
            form: {
                name : null,
                district : null,
                address : null,
                telephone : null,
                mobile : null,
                email : null,
                fax : null,
            },
        }
    },
    methods: {
        search(searchItem){
            this.$inertia.replace(route('polytechnic.index', {'search': searchItem}))
        }
    },
}
</script>

<style scoped>

</style>
