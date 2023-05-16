<template>
    <Head>
        <title>Generate New Invoice</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Generate New Invoice</page-header>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can">
                        <template #first>
                            <div class="flex text-center">
                                <Back :back-url="route('invoice.index')"></Back>
                                <label for="semester" class="flex-col mx-3 text-center">Semester</label>
                                <select name="semester" id="semester" class="form-control flex-col" v-model="semester" @change="changeSemester">
                                    <option value=""></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                                <InputError :message="errors.semester"></InputError>
                            </div>
                        </template>
                        <template #second>
                            <div class="flex">
                                <label for="sessions" class="flex-col mx-3 text-center">Sessions</label>
                                <select name="sessions" id="sessions" class="form-control" v-model="academic_session" @change="changeSession">
                                    <option value=""></option>
                                    <option v-for="session in sessions" :value="session.session">{{session.session}}</option>
                                </select>
                                <InputError :message="errors.academic_session"></InputError>
                            </div>
                        </template>
                        <template #third>
                            <div class="flex">
                                <label for="invoice_month">Month</label>
                                <MonthPickerInput @change="changeYearMonth"></MonthPickerInput>
                                <InputError :message="errors.invoice_month"></InputError>
                                <Button class="btn btn-success mx-3" @click="generateInvoice">Generate</Button>
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-bordered table-auto">
                        <thead>
                        <tr class="align-middle">
                            <th rowspan="2">Sl.#</th>
                            <th rowspan="2">Billable <br>
                                <input type="checkbox" class="rounded-full" v-model="allSelectedStudents" @change="allSelectedStudent"></th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">IBBL Branch</th>
                            <th rowspan="2">IBBL Account</th>
                            <th :colspan="feeTypes != null ? feeTypes.length : 0">Tuition Fees</th>
                            <th rowspan="2">Amount</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th v-for="feeType in feeTypes">
                                {{feeType.fee_type}}
                                <input type="checkbox" v-model="billableFee[feeType.fee_type]" class="form-check-input">
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(student, index) in students">
                            <td>{{ index + 1 }}</td>
                            <td class="text-center">
                                <input type="checkbox" ref="selectedStudent" :checked="allSelectedStudents" v-model="selected_student[student.id]" class="rounded-full">
                            </td>
                            <td>{{ student.name }}</td>
                            <td>{{ student.bank_branch }}</td>
                            <td class="text-center">{{ student.bank_account }}</td>
                            <td class="text-center" v-for="fee in student.fees">
                                <span v-if="(fee.fee_type === 'MMA' && student.results[0]?.status !== 'Passed') || semester == 1">0</span>
                                <span v-else>
                                     {{ isPaid(student.invoice_details, fee.fee_type).length === 0 ?  fee.amount : 0}}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ individualTotal(student.fees, student.invoice_details, student.results[0]?.status) }}
                            </td>
                            <td class="text-center">{{student.results[0]?.status}}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" style="text-align: right">Total</td>
                            <td class="text-center">{{ totalInvoiceAmount() }}</td>
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
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Button from "@/Shared/Button";
import InputError from "@/Components/InputError";
import { MonthPickerInput } from 'vue-month-picker'
import Back from "@/Shared/Back";

function GET() {
    const data = [];
    for(let x = 0; x < arguments.length; ++x){
        if(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)"))) !== null){
            data.push(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)")))[1])
        }
    }
    return data;
}
export default {
    name: "Generate",
    props: ['can', 'errors', 'students', 'sessions', 'feeTypes'],
    components: {Back, MonthPickerInput, InputError, Button, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            studentList : this.students,
            editingFee: {

            },
            academic_session: GET('polytechnic_session')[0],
            semester: GET('semester')[0],
            invoice_month: GET('invoice_month')[0],
            billableFee: {

            },
            selected_student: {

            },
            allSelectedStudents: false
        }
    },
    methods: {
        isPaid(invoiceDetails, fee_type){
            const _this = this
            const details = invoiceDetails.filter((details) => {
                return fee_type === details.fee_type && (fee_type !== "MMA" || _this.invoice_month === details.invoice_month)
            })
            return details;
        },
        individualTotal(fees, invoice_details, resultStatus){
            let total = 0;
            const self = this
            fees.map(function (fee) {
                let details = invoice_details.filter(d => {
                    return fee.fee_type === d.fee_type && (fee.fee_type  !== "MMA" || self.invoice_month === d.invoice_month) && (fee.fee_type  !== "MMA" || resultStatus ==="Passed")
                })
                if (details.length === 0){
                    if(self.billableFee[fee.fee_type]){
                        total += fee.fee_type  !== "MMA" || resultStatus ==="Passed" ? fee.amount : 0
                    }
                }
            })
            return total
        },
        totalInvoiceAmount(){
            let total = 0;
            const self = this
            this.students.map(function (student){
                student.fees.map(function (fee) {
                    let details = student.invoice_details.filter(d => {
                        return fee.fee_type === d.fee_type && (fee.fee_type  !== "MMA" || self.invoice_month === d.invoice_month)
                    })
                    if (details.length === 0){
                        if(self.billableFee[fee.fee_type]){
                            total += fee.fee_type  !== "MMA" || student.results[0]?.status === "Passed" ? fee.amount : 0;
                        }
                    }
                })
            })
            return total;
        },
        changeSession(){
            this.$inertia.replace(route('invoice.create', {'polytechnic_session' : this.academic_session, 'semester': this.semester}))
        },
        changeSemester(){
            this.$inertia.replace(route('invoice.create', {'polytechnic_session' : this.academic_session, 'semester': this.semester}))
        },
        search(params){
            this.$inertia.replace(route('invoice.create',{'search': params}))
        },
        generateInvoice(){
            this.$inertia.post(route('invoice.store', {
                'academic_session' : this.academic_session,
                'semester': this.semester,
                'invoice_month': this.invoice_month,
                'billableFee': JSON.stringify(this.billableFee),
                'selected_students': JSON.stringify(this.selected_student)
            }))
        },
        changeYearMonth(date){
            let invoice_month = date.month+" "+date.year
            this.invoice_month = invoice_month
        },
        allSelectedStudent(){
            let selected_student = this.selected_student
            if(this.allSelectedStudents){
                if (this.students){
                    this.students.map((student) => {
                        selected_student[student.id] = true
                    })
                }
            }else {
                selected_student = {}
            }
            this.selected_student = selected_student
        }
    },
    computed:{
    },
    mounted() {
        let billableFee= this.billableFee
        if (this.feeTypes){
            this.feeTypes.map(function (fee) {
                billableFee[fee.fee_type] = true
            })
        }
        // let selected_student = this.selected_student
        // if (this.students){
        //     this.students.map((student) => {
        //         selected_student[student.id] = true
        //     })
        // }

    }
}
</script>

<style>
.month-picker-input{
    display: block!important;
    width: 100%!important;
    padding: 0.375rem 0.75rem!important;
    font-size: 1rem!important;
    font-weight: 400!important;
    line-height: 1.5!important;
    color: #212529!important;
    background-color: #fff!important;
    background-clip: padding-box!important;
    border: 1px solid #ced4da!important;
    -webkit-appearance: none!important;
    -moz-appearance: none!important;
    appearance: none!important;
    border-radius: 0.25rem!important;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out!important;
}
</style>
