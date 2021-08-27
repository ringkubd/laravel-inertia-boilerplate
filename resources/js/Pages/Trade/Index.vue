<template>
    <Authenticated>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trade Management</h2>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card flex min-h-screen mt-5">
                    <div class="card-header">
                        <CardHeader  :can="can" :create="route('trade.create')" :index="route('trade.index')" :search-method="search"></CardHeader>
                    </div>
                    <div class="card-body">
                        <table class="table table-secondary table-striped text-center">
                            <thead>
                            <tr>
                                <th>Sl#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(trade, index) in trades.data">
                                <td>{{index + 1}}</td>
                                <td>{{trade.name}}</td>
                                <td>{{tradeType(trade.is_madrasa)}}</td>
                                <td>
                                    <Actions :can="can" :edit-url="route('trade.edit', trade.id)" :delete-url="route('trade.destroy', trade.id)"></Actions>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <paginator :paginator="trades"></paginator>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>


<script>
import moment from "moment";
import Authenticated from "@/Layouts/Authenticated";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
import CardHeader from "@/Shared/CardHeader";

export default {
    name: "Index",
    props: ['trades', 'can'],
    components: {CardHeader, Paginator, Actions, Authenticated},
    data(){
        return {
        }
    },
    methods: {
        modifiedFromNow(dateTime){
            return moment(dateTime, 'YYYY-MM-DDTh:mm:ss').fromNow()
        },
        search(param){
            this.$inertia.replace(route('trade.index', {'search': param}))
        },
        tradeType(isMadrasa){
            return isMadrasa ? "Madrasa" : "Polytechnic"
        }
    },
    computed() {
    }
}
</script>

<style scoped>

</style>
