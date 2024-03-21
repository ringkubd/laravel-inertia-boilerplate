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
                    <CardHeader :can="can" :create="route('invoice.create')" :search-method="search">
                        <template #second>
                            <Select2
                                :options="academic_sessions.map(s => ({id: s.session, text: s.session}))"
                                v-model="search_form.academic_session"
                                id="academic_session"
                                @select="localSearch"
                            />
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>Sl.#</th>
                            <th>Invoice ID.</th>
                            <th>Session</th>
                            <th>Semester</th>
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
                            <td>{{ invoice.session }}</td>
                            <td>{{ semester(invoice.semester) }}</td>
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
import Select2 from "@/Components/Select2.vue";
export default {
    name: "Index",
    props: ['can', 'invoices', 'academic_sessions'],
    components: {Select2, Paginator, Actions, PageHeader, CardHeader, Authenticated},
    data(){
       return {
           search_form: {
               academic_session: ''
           }
       }
    },
    methods: {
        search(params){
            this.$inertia.replace(route('invoice.index', {'search': params}))
        },
        localSearch(){
            this.$inertia.replace(route('invoice.index', {'search': this.search_form.academic_session}))
        },
        semester(semester){
            switch (semester) {
                case 1:
                    return "1st";
                case 2:
                    return "2nd";
                case 3:
                    return "3rd";
                case 4:
                    return "4th";
                case 5:
                    return "5th";
                case 6:
                    return "6th";
                case 7:
                    return "7th";
                case 8:
                    return "8th";
            }
        }
    }
}
</script>

<style scoped>

</style>
