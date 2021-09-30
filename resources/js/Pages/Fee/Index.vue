<template>
    <Head>
        <title>Fee's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fee Information</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('fee.create')" :search-method="search"></CardHeader>
                </div>
                <table class="table table-secondary table-striped text-center">
                    <thead>
                    <tr>
                        <th>SL#</th>
                        <th>Session</th>
                        <th>Trade</th>
                        <th>Semester</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(fee, index) in data.data" :key="fee.id">
                        <th>{{index + 1}}</th>
                        <td>{{fee.session}}</td>
                        <td>{{fee.trade}}</td>
                        <td>{{fee.semester}}</td>
                        <td>{{fee.fee_type}}</td>
                        <td>{{fee.amount}}</td>
                        <td>
                            <Actions :can="can" :delete-url="route('fee.destroy', fee.id)" :edit-url="route('fee.edit', fee.id)"></Actions>
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
            this.$inertia.replace(route('fee.index', {'search': searchItem}))
        }
    },
}
</script>

<style scoped>

</style>
