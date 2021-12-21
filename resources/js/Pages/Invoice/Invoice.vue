<template>
    <Head>
        <title>Invoice</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Generate New Invoice</page-header>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
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
                    <table class="table table-secondary table-bordered">
                        <thead>
                        <tr>
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
                            <td>{{ getFirstWord(invoice.student.polytechnic_trade_id) }}</td>
                            <td>{{ getFirstWord(invoice.bank_branch, ',') }}</td>
                            <td>{{ invoice.bank_account }}</td>
                            <td v-for="fee in invoice.details">{{ fee.amount }}</td>
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
    name: "Invoice",
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo'],
    components: {Button, Back, CardHeader, Authenticated},
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
                ]
            }

            this.$htmlToPaper('printme', printOptions, () => {
                console.log('Printing finished');
            });
        },
        getFirstWord(str, delimter = " ") {
            let spaceIndex = str.indexOf(delimter);
            return spaceIndex === -1 ? str : str.substr(0, spaceIndex);
        }
    }
}
</script>

<style scoped>
</style>
