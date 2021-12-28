<template>
    <Head>
        <title>Madrasa's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <PageHeader>Madrasa Information</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('madrasa.create')" :search-method="search">

                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>SL.#</th>
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
                        <tr v-for="(madrasa, index) in data" :key="madrasa.id">
                            <td>{{index + 1}}</td>
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
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import PageHeader from "@/Shared/PageHeader";
export default {
    components: {
        PageHeader,
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
