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
                            <Button class="btn btn-success mma" @click="print">Print</Button>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body" id="printme">
                    <table class="table table-secondary border-0 print:border-0">
                        <thead class="border-0 print:border-0">
                        <tr class="border-0 print:border-0">
                            <th :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" rowspan="4" class="border-0 print:border-0">
                                Annex - A
                                <p style="padding: 0!important; margin: 0!important;">Academic Year: {{ basicInfo.session }}</p>
                                <div class="text-left" style="text-align: left">
                                    Semester: <span class="font-normal ml-5">{{ basicInfo.semester }}</span>
                                    <br>
                                    Date: <span class="font-normal ml-5">{{ basicInfo.invoice_date }}</span>
                                    <div v-if="last_mma != 0">
                                        MMA No. : {{ basicInfo.invoice_no }}
                                    </div>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <thead class="border-0 print:border-0">
                        <tr class="align-middle border-0 print:border-0" style="background-color: #e0d5d5!important;">
                            <th rowspan="2">Sl.#</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Trade</th>
                            <th rowspan="2">IBBL Branch</th>
                            <th rowspan="2">IBBL Account</th>
                            <th :colspan="feeTypes != null ? feeTypes.length : 0">Tuition Fees</th>
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr style="background-color: #e0d5d5!important;">
                            <th v-for="feeType in feeTypes">
                                {{feeType}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(invoice, index) in data">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td style="width: 20%!important;">{{ invoice.student_name }}</td>
                            <td style="width: 15%!important;">{{ getFirstWord(invoice.student.polytechnic_trade_id) }}</td>
                            <td style="width: 25%!important;">{{ invoice.bank_branch }}</td>
                            <td>{{ invoice.bank_account }}</td>
                            <td class="text-center" v-for="ty in feeTypes">{{tuition_fees(invoice.details, ty)}}</td>
                            <td class="text-center">{{invoice.amount}}</td>
                        </tr>
                        <tr rowspan="2" style="border-top: 2px solid gray!important; color: black!important; font-weight: 600">
                            <th :colspan="5+ (feeTypes != null ? feeTypes.length : 0)" class="total" style="text-align: right">Total</th>
                            <th class="text-center">{{totalInvoiceAmount()}}</th>
                        </tr>
                        </tbody>
                        <tfoot class="footer" style="position: static; width: 	21.0cm!important; bottom: 0;">
                        <tr style="border-bottom: 0!important;">
                            <td colspan="2" class="text-center" style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Jr. Officer (Projects)</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td colspan="2"  class="text-center" style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Program Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td colspan="3" class="text-center" style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Program Coordinator</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center" style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span> Asst. Accounts Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td colspan="2" class="text-center"  style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Accounts Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td colspan="3" class="text-center" style="padding-top: 40px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Chief Executive Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

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
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo', 'last_mma'],
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
                    '/css/app.css'
                ]
            }

            this.$htmlToPaper('printme', printOptions, (e) => {
                console.log(this);
            });
        },
        getFirstWord(str, delimter = " ") {
            let spaceIndex = str.indexOf(delimter);
            return spaceIndex === -1 ? str : str.substr(0, spaceIndex);
        },
        tuition_fees(details, fee_types){
            let amount = details.filter((i) => {
                return fee_types === i.fee_type
            })[0]?.amount
            return amount ? amount : 0
        }
    }
}
</script>

<style scoped>
</style>
