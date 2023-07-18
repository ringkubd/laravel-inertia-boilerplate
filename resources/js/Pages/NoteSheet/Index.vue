<template>
    <Head>
        <title>Note Sheet</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Note Sheet Management</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <NavLink :href="route('note_sheet.create')" class="bg-green-600 hover:bg-green-800 px-4 py-2 rounded align-middle text-white text-capitalize font-bold font-sans">
                                    Generate
                                </NavLink>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-borderd">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Session</th>
                                <th>Semester</th>
                                <th>Inv. Date</th>
                                <th>Inv. ID.</th>
                                <th>Inv. Month</th>
                                <th>Inv. No</th>
                                <th>No. of St.</th>
                                <th>Fee Type</th>
                                <th>Page No</th>
                                <th>Serial No</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(ns, i) in note_sheet.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{ ns.invoice?.session }}</td>
                                <td>{{ ns.invoice?.semester }}</td>
                                <td>{{ ns.invoice?.invoice_date }}</td>
                                <td>{{ ns.invoice_id }}</td>
                                <td>{{ ns.invoice?.invoice_month }}</td>
                                <td>{{ ns.invoice?.invoice_no }}</td>
                                <td>{{ ns.invoice?.number_of_student }}</td>
                                <td>{{ JSON.parse(ns.invoice?.fee_type).join(", ") }}</td>
                                <td>{{ ns.page_no }}</td>
                                <td>{{ ns.serial_no }}</td>
                                <td>{{ parseFloat(ns.invoice?.total_amount).toFixed(2) }}</td>
                                <td><Actions :can="can" :detail-url="route('note_sheet.show', ns.id)" /></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Label from "@/Components/Label";
import Button from "@/Shared/Button";
import Actions from "@/Shared/Actions";
import NavLink from "@/Components/NavLink";
export default {
    name: "Index",
    props: ['note_sheet', 'can'],
    components: {NavLink, Actions, Button, Label, CardHeader, PageHeader, Authenticated}
}
</script>

<style scoped>

</style>
