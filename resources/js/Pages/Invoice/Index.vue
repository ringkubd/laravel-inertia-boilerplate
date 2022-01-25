<template>
    <Head>
        <title>Invoice Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>
                Invoice Management
            </page-header>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('invoice.create')" :search-method="search"></CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>Sl.#</th>
                            <th>Invoice ID.</th>
                            <th>Invoice Month</th>
                            <th>Invoice Type</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(invoice, index) in invoices.data">
                            <td>{{ index + 1 }}</td>
                            <td>{{ invoice.invoice_id }}</td>
                            <td>{{ invoice.invoice_month }}</td>
                            <td>{{ JSON.parse(invoice.fee_type).toString() }}</td>
                            <td>{{ invoice.invoice_date }}</td>
                            <td>{{ invoice.total_amount }}</td>
                            <td>
                                <Actions :can="can" :delete-url="route('invoice.destroy', invoice.invoice_id)" :edit-url="route('invoice.edit', invoice.invoice_id)" :detail-url="route('invoice.show', invoice.invoice_id)"></Actions>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator :paginator="invoices" />
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import PageHeader from "@/Shared/PageHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
export default {
    name: "Index",
    props: ['can', 'invoices'],
    components: {Paginator, Actions, PageHeader, CardHeader, Authenticated},
    data(){

    },
    methods: {
        search(params){
            this.$inertia.replace(route('invoice.index', {'search': params}))
        }
    }
}
</script>

<style scoped>

</style>
