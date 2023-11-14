<template>
    <Head>
        <title>Invoice</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Generate New Invoice</page-header>
        </template>
        <div class="container-fluid min-h-screen">
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
                    <div class="flex flex-row w-full justify-between mb-3">
                        <div></div>
                        <div class="text-center">
                            <strong><h2>IsDB-BISEW 4-Year Diploma Scholarship Program</h2></strong>
                        </div>
                        <div></div>
                    </div>
                    <table class="table table-secondary border-0 print:border-0">
                        <thead class="border-0 print:border-0">
                        <tr class="border-0 print:border-0">
                            <th :colspan="5+ (feeTypes != null ? feeTypes.length : 0)" rowspan="4" class="border-0 print:border-0">
                                <div class="text-left" style="text-align: left">
                                    <table class="">
                                        <tbody class="">
                                        <tr class="">
                                            <th class="text-left pr-6">Academic Year:</th>
                                            <td class="text-left px-6"> {{ basicInfo.session }}</td>
                                        </tr>
                                        <tr class="">
                                            <th class="text-left pr-6">Semester Continuing:</th>
                                            <td class="text-left px-6">{{ ordinal_suffix_of(basicInfo.semester) }}</td>
                                        </tr>
                                        <tr class="">
                                            <th class="text-left pr-6">Raised Date:</th>
                                            <td class="text-left px-6">{{ moment(basicInfo.invoice_date).format('DD MMM Y') }}</td>
                                        </tr>
                                        <tr class="" v-if="last_mma != 0">
                                            <th class="text-left pr-6">MMA Number.:</th>
                                            <td class="text-left px-6">
                                                {{ordinal_suffix_of(basicInfo.invoice_no)}} of {{ordinal_suffix_of(basicInfo.semester)}}, {{ basicInfo.invoice_no * basicInfo.semester }}/48
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </th>
                            <th colspan="2">
                                <h4>Annex - A</h4>
                            </th>
                        </tr>
                        </thead>
                        <thead class="border-1 print:border-1">
                        <tr class="align-middle border-1 print:border-1" style="background-color: #e0d5d5!important;">
                            <th rowspan="2">Sl.#</th>
                            <th rowspan="2">Roll</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Trade</th>
                            <th rowspan="2">IBBL Branch</th>
                            <th rowspan="2">IBBL Account</th>
                            <th :colspan="feeTypes != null ? feeTypes.length : 0">Tuition Fees</th>
                            <th rowspan="2">Total</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr class="border-1" style="background-color: #e0d5d5!important;">
                            <th v-for="feeType in feeTypes">
                                {{feeType}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(invoice, index) in data" class="border-1">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td class="text-center">{{ invoice.student?.polytechnic_roll }}</td>
                            <td style="width: 20%!important;">{{ invoice.student_name }}</td>
                            <td style="width: 15%!important;">{{ getFirstWord(invoice.student.polytechnic_trade_id) }}</td>
                            <td style="width: 25%!important;">{{ invoice.bank_branch }}</td>
                            <td>{{ invoice.bank_account }}</td>
                            <td class="text-center" v-for="ty in feeTypes">{{tuition_fees(invoice.details, ty)}}</td>
                            <td class="text-center">{{invoice.amount}}</td>
                            <td class="text-right">{{remarks(invoice.result_status, invoice.payment_slip, basicInfo)}}</td>
                        </tr>
                        <tr rowspan="2" style="border: 1px solid rgb(0,0,0)!important; color: black!important; font-weight: 600">
                            <th :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" class="total" style="text-align: right!important; border: 1px solid rgb(0,0,0)!important;">Total Amount</th>
                            <th class="text-center">{{totalInvoiceAmount()}}</th>
                            <th class="text-center"></th>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr class="border-0">
                            <td colspan="10" style="padding-left: 2.5em!important; border: 0!important;">
                                <strong>Note: </strong>
                                <ul id="note">
                                    <li>DS- Document submitted.</li>
                                    <li>DNS- Document Not submitted.</li>
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <table class="table-auto w-full mt-10">
                        <tbody>
                        <tr style="border-bottom: 0!important;">
                            <td class="text-center" style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Program Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td class="text-center" style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Program Coordinator</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td class="text-center" style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2"/>
                                    <span>Sr. Program Coordinator</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <td class="text-center" style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Accounts Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td class="text-center"  style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Sr. Accounts Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                            <td class="text-center" style="padding-top: 50px!important;">
                                <div class="text-center flex flex-col justify-center items-center">
                                    <hr style="color: black!important;" class="w-1/2">
                                    <span>Chief Executive Officer</span>
                                    <span>IsDB-BISEW</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
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
import PageHeader from "@/Shared/PageHeader.vue";
import moment from "moment";
export default {
    name: "Invoice",
    computed: {
        moment() {
            return moment
        }
    },
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo', 'last_mma'],
    components: {PageHeader, Button, Back, CardHeader, Authenticated},
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
        },
        remarks(status, paymentSlip, basicInfo){
            const feeType = JSON.parse(basicInfo.fee_type)
            return status === "Dropout" ? status : feeType.length === 1 && feeType.includes('MMA') ? status :  feeType.includes('Sem. Fee') && paymentSlip.length ? 'DS' : paymentSlip.length ? status : 'DNS'
        }
    }
}
</script>

<style>
ul#note  {
    --icon-space: 1.3em;
    list-style: none;
    padding: 0;
}
ul#note li:before {
    content: "*";
    display: inline-block;
    margin-left: calc( var(--icon-space) * -1 );
    width: var(--icon-space);
}
li{
    list-style-type: '*';
}
</style>
