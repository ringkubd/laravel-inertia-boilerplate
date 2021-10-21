<template>
    <Head>
        <title>Invoice</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Generate New Invoice</page-header>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :search-method="search">
                        <template #first>
                            <Back :back-url="route('invoice.index')"></Back>
                        </template>
                        <template #second>
                            <Button class="btn btn-success" @click="print">Print</Button>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body" id="printme">
                    <table class="table table-secondary table-bordered text-center">
                        <thead>
                        <tr style="border-left: solid white 2px; border-right: solid white 2px; border-top: solid white 2px;">
                            <th :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" rowspan="4">
                                Invoice
                                <br>
                                <div class="text-left">
                                    Invoice: <span class="font-normal ml-5">{{ basicInfo.invoice_id }}</span>
                                    <br>
                                    Date: <span class="font-normal ml-5">{{ basicInfo.invoice_date }}</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <thead>
                        <tr>
                            <th rowspan="2">Sl.#</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Trade</th>
                            <th rowspan="2">IBBL Branch</th>
                            <th rowspan="2">IBBL Account</th>
                            <th :colspan="feeTypes != null ? feeTypes.length : 0">Tuition Fees</th>
                            <th rowspan="2">Amount</th>
                        </tr>
                        <tr>
                            <th v-for="feeType in feeTypes">
                                {{feeType}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(invoice, index) in data">
                            <td>{{ index + 1 }}</td>
                            <td>{{ invoice.student_name }}</td>
                            <td>{{ invoice.student.polytechnic_trade_id }}</td>
                            <td>{{ invoice.bank_branch }}</td>
                            <td>{{ invoice.bank_account }}</td>
                            <td v-for="fee in invoice.details" :name="fee.fee_type" :invoiceDetalsId="fee.id" :contenteditable="contentEditable" @input="changeFees">{{ fee.amount }}</td>
                            <td>{{invoice.amount}}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td :colspan="5+ (feeTypes != null ? feeTypes.length : 0)" style="text-align: right">Total</td>
                            <td>{{totalInvoiceAmount()}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Back from "@/Shared/Back";
import Button from "@/Shared/Button";
export default {
    name: "Edit",
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo'],
    components: {Button, Back, CardHeader, Authenticated},
    data(){
        return {
            contentEditable: this.can.update
        }
    },
    mounted(){
        console.log(this.can.update)
    },
    methods: {
        search(params){

        },
        totalInvoiceAmount(){
            let total = 0;
            const self = this
            this.data.map(function (invoice){
                total += invoice.amount
            })
            return total;
        },
        print: function() {

            const printOptions = {
                name: '_blank',
                specs: [
                    'fullscreen=yes',
                    'titlebar=yes',
                    'scrollbars=yes'
                ],
                styles: [
                    '/css/custom_print.css',
                    '/css/app.css',
                ]
            }

            this.$htmlToPaper('printme', printOptions, () => {
                console.log('Printing finished');
            });
        },
        changeFees(e){
            let invoiceDetailsId = e.target.getAttribute('invoiceDetalsId');
            let amount = e.target.innerText;
            this.$inertia.put(route('invoice.update', invoiceDetailsId), { amount: amount })
        }
    }
}
</script>

<style scoped>

</style>
