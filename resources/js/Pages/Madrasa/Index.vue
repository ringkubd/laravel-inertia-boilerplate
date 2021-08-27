<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Madrasa Information</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('madrasa.create')" :search-method="search"></CardHeader>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>District</th>
                        <th>Address</th>
                        <th>Telephone</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Fax</th>
                        <th>Principal</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="madrasa in data" :key="madrasa.id">
                        <td>{{madrasa.name}}</td>
                        <td>{{madrasa.district}}</td>
                        <td>{{madrasa.address}}</td>
                        <td>{{madrasa.telephone}}</td>
                        <td>{{madrasa.mobile}}</td>
                        <td>{{madrasa.email}}</td>
                        <td>{{madrasa.fax}}</td>
                        <td></td>
                        <td>
                            <Actions :can="can" :delete-url="route('madrasa.destroy', madrasa.id)" :edit-url="route('madrasa.edit', madrasa.id)"></Actions>
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
            this.$inertia.replace(route('madrasa.index', {'search': searchItem}))
        }
    },
}
</script>

<style scoped>

</style>
