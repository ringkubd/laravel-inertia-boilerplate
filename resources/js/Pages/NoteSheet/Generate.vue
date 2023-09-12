<template>
    <Head>
        <title>Generate Note sheet</title>
    </Head>
    <authenticated>
        <template #header>
            <PageHeader>
                Generate Note Sheet
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <Back :back-url="route('note_sheet.index')"/>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body">
                        <div class="mx-auto max-w-7xl min-h-screen shadow-lg">
                            <form action="" method="post">
                                <div class="mx-auto w-1/2">
                                    <label for="invoice">Invoice</label>
                                    <select class="form-control" name="invoice" id="invoice" v-model="invoice_id" @change="invoiceInfo">
                                        <option value=""></option>
                                        <option v-for="inv in invoices" :value="inv.invoice_id">{{inv.session}}-{{inv.semester}}-{{inv.invoice_id}}-{{JSON.parse(inv.fee_type).join()}}</option>
                                    </select>
                                </div>
                                <div class="mx-auto w-1/2">
                                    <label for="template">Template</label>noteSheetText
<!--                                    <select class="form-control" name="invoice" id="template" v-model="template_id" @change="templateInfo">-->
<!--                                        <option value=""></option>-->
<!--                                        <option v-for="temp in note_sheet_template" :value="temp.id">{{temp.title}}</option>-->
<!--                                    </select>-->
                                    <select class="form-control" name="invoice" id="template" v-model="template_id" @change="templateInfo">
                                        <option value=""></option>
                                        <option v-for="temp in noteSheetText" :value="temp[0].note_type">{{temp[0].note_type.toUpperCase()}}</option>
                                    </select>
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'semester' || template_id === 'admission'">
                                    <label for="circular_date">Circular Date</label>
                                    <input type="date" id="circular_date" name="circular_date" v-model="circular_date" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'admission'">
                                    <label for="dakhil_exam_year">Dakhil Exam Year</label>
                                    <input type="number" min="2010" max="2050" id="dakhil_exam_year" name="dakhil_exam_year" v-model="dakhil_exam_year" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'admission'">
                                    <label for="dakhil_exam_student">How many students took the Dakhil exam?</label>
                                    <input type="number" id="dakhil_exam_student" name="dakhil_exam_student" v-model="dakhil_exam_student" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'admission'">
                                    <label for="dakhil_exam_passed_student">How many students passed the Dakhil exam?</label>
                                    <input type="number" id="dakhil_exam_passed_student" name="dakhil_exam_passed_student" v-model="dakhil_exam_passed_student" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'admission'">
                                    <label for="admission_selected_student">Apply Student</label>
                                    <input type="number" id="admission_selected_student" name="admission_selected_student" v-model="admission_selected_student" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'admission'">
                                    <label for="admission_enrolled_student">Admitted Student</label>
                                    <input type="number" id="admission_enrolled_student" name="admission_enrolled_student" v-model="admission_enrolled_student" class="form-control">
                                </div>
                                <div class="mx-auto w-1/2" v-if="template_id === 'semester' || template_id === 'admission'">
                                    <button class="btn btn-success" type="button" @click="update">Update</button>
                                </div>
                            </form>
                            <div class="my-4 sm:mx-10 p-4 shadow flex flex-row max-w-7xl">
                                <div class="float-left justify-start items-start flex-1">Date: {{ new Intl.DateTimeFormat('en', { dateStyle: 'full' }).format(new Date()) }}</div>
                                <div class="float-right flex-1 justify-end items-end">Page # {{ page_no }}</div>
                            </div>
                            <div class="my-4 sm:mx-10 p-4 shadow" v-html="template_text" contenteditable="true" @input="noteEdit($event.target.innerHTML)">
                            </div>
                            <Button type="submit" class="bg-[#36C383] hover:bg-green-700 float-right my-4 sm:mx-10 p-4 shadow" @click="storeNote()">Submit</Button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Back from "@/Shared/Back";
import Button from "@/Shared/Button";
import moment from "moment";
import number2wordEnglish from "number2english_word";

export default {
    name: "Generate",
    props: ['invoices', 'note_sheet_template', 'page_no', 'serial_no', 'noteSheetText'],
    components: {Button, Back, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            invoice_id: "",
            template_id: "",
            template_text: "",
            invoice_info: {},
            circular_date: "",
            admission_selected_student: "",
            admission_enrolled_student: "",
            dakhil_exam_year: "",
            dakhil_exam_student: "",
            dakhil_exam_passed_student: "",
            note: '',
            mma_table: '',
            admission_table: '',
        }
    },
    methods: {
        invoiceInfo(){
            const invInfo = this.invoices.filter((inv) => {
                return inv.invoice_id === this.invoice_id
            })
            console.log(invInfo[0])
            this.invoice_info = invInfo[0]
        },
        async templateInfo(){
            const noteST = this.noteSheetText[this.template_id]
            let content = noteST[0]?.content
            const app = this;
            let invoice = {};
            let isMMA = 0;

            if (content.search('[mma_table]') !== -1){
                await axios.get(route('mma_table', this.invoice_id))
                    .then(function (d) {
                        app.mma_table = d.data['table']
                        invoice = d.data['invoice']
                    })
                isMMA = 1;
            }
            if (content.search('[admission_table]') !== -1){
                await axios.get(route('admission_table', this.invoice_id))
                    .then(function (d) {
                        app.ad_table = d.data
                    })
            }
            let didNotSubmitDocument = parseInt(this.invoice_info?.number_of_student) - parseInt(this.invoice_info?.total_student);
            let faildStudent =  parseInt(this.invoice_info?.number_of_student) - parseInt(invoice.eligible_student);
            let rest_student = didNotSubmitDocument > 0 ? `Rest ${didNotSubmitDocument} students did not submit payment slips of the respective Polytechnic Institute confirming payments and excluded from the list.` : "";
            if (isMMA === 1){
                rest_student = faildStudent > 0 ? `Rest ${faildStudent} students have failed to pass all subjects successfully and got referred in the ${this.semesterString(parseInt(this.invoice_info?.semester) - 1)} semester
final exam results and excluded from this list. However, they may be included if they pass
and continue.` : "";
            }
            let all = "";
            if (faildStudent === 0){
                all = "all"
            }

            // console.log(number2text(20))

            const numberFormat = new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' })
            this.template_text = `<ol start="${this.serial_no}">`
            this.template_text += `<li>`+content.replaceAll('[number_of_student]', this.invoice_info?.number_of_student)
                .replaceAll('[section]', this.invoice_info?.section)
                .replaceAll('[total_student]', this.invoice_info?.total_student)
                .replaceAll('[nd_student]', this.invoice_info?.nd_student)
                .replaceAll('[subject]', this.invoice_info?.subject)
                .replaceAll('[semester]', this.semesterString(this.invoice_info?.semester))
                .replaceAll('[prev_semester]', this.semesterString(parseInt(this.invoice_info?.semester) - 1))
                .replaceAll('[bill_group]', this.invoice_info?.bill_group)
                .replaceAll('[current_date]', this.invoice_info?.current_date)
                .replaceAll('[invoice_date]', this.invoice_info?.invoice_date)
                .replaceAll('[total_amount]', numberFormat.format(this.invoice_info?.total_amount)+ ' '+ ` (${number2wordEnglish(this.invoice_info?.total_amount)})`)
                .replaceAll('[idb_account]', this.invoice_info?.idb_account)
                .replaceAll('[bill_period]', this.invoice_info?.bill_period)
                .replaceAll('[session]', this.invoice_info?.session)
                .replaceAll('[bill_type]', JSON.parse(this.invoice_info?.fee_type))
                .replaceAll('[eligible_student]', invoice.eligible_student)
                .replaceAll('[failed_student]', invoice.failed_student)
                .replaceAll('[mma_table]', app.mma_table)
                .replaceAll('[admission_table]', app.ad_table)
                .replaceAll('[rest_student]', rest_student)
                .replaceAll('[all]', all)
                .replaceAll('[invoice_month_year]', moment(this.invoice_info?.invoice_month).format("MMMM YYYY"))
            this.template_text += `<li>`+noteST[1]?.content.replaceAll('[number_of_student]', this.invoice_info?.number_of_student)
                .replaceAll('[section]', this.invoice_info?.section)
                .replaceAll('[total_student]', this.invoice_info?.total_student)
                .replaceAll('[nd_student]', this.invoice_info?.nd_student)
                .replaceAll('[subject]', this.invoice_info?.subject)
                .replaceAll('[semester]', this.semesterString(this.invoice_info?.semester))
                .replaceAll('[prev_semester]', this.semesterString(parseInt(this.invoice_info?.semester) - 1))
                .replaceAll('[bill_group]', this.invoice_info?.bill_group)
                .replaceAll('[current_date]', this.invoice_info?.current_date)
                .replaceAll('[invoice_date]', this.invoice_info?.invoice_date)
                .replaceAll('[total_amount]', numberFormat.format(this.invoice_info?.total_amount)+ ' ' + ` (${number2wordEnglish(this.invoice_info?.total_amount)})`)
                .replaceAll('[idb_account]', this.invoice_info?.idb_account)
                .replaceAll('[bill_period]', this.invoice_info?.bill_period)
                .replaceAll('[session]', this.invoice_info?.session)
                .replaceAll('[bill_type]', JSON.parse(this.invoice_info?.fee_type))
                .replaceAll('[mma_table]', app.mma_table)
                .replaceAll('[eligible_student]', invoice.eligible_student)
                .replaceAll('[failed_student]', parseInt(this.invoice_info?.number_of_student) - parseInt(invoice.eligible_student))
                .replaceAll('[rest_student]', rest_student)
                .replaceAll('[all]', all)
                .replaceAll('[invoice_month_year]', moment(this.invoice_info?.invoice_month).format("MMMM YYYY"))
                // .replace('<ol>', `<ol start={this.serial_no}>`)
            this.template_text += "</ol>"
            this.note = this.template_text
        },
        noteEdit(v){
            this.note = v
        },
        storeNote(){
            this.$inertia.post(route('note_sheet.store'), {
                note_text: this.note,
                page_no: this.page_no,
                serial_no: this.serial_no,
                invoice_id: this.invoice_id
            })
        },
        semesterString(sem){
            switch (sem) {
                case 1:
                    return `1<sup>st</sup>`
                case 2:
                    return `2<sup>nd</sup>`
                case 3:
                    return `3<sup>rd</sup>`
                case 4:
                    return `4<sup>th</sup>`
                case 5:
                    return `5<sup>th</sup>`
                case 6:
                    return `6<sup>th</sup>`
                case 7:
                    return `7<sup>th</sup>`
                case 8:
                    return `8<sup>th</sup>`
            }
        },
        update(){
            if (this.dakhil_exam_year !== ""){
                this.template_text = this.template_text.replaceAll('[dakhil_exam_year]', this.dakhil_exam_year)
                this.note = this.template_text;
            }
            if (this.dakhil_exam_student !== ""){
                this.template_text = this.template_text.replaceAll('[dakhil_exam_student]', this.dakhil_exam_student)
                this.note = this.template_text;
            }
            if (this.dakhil_exam_passed_student !== ""){
                this.template_text = this.template_text.replaceAll('[dakhil_pass_student]', this.dakhil_exam_passed_student)
                this.note = this.template_text;
            }
            if (this.admission_selected_student !== ""){
                this.template_text = this.template_text.replaceAll('[admission_selected_student]', this.admission_selected_student)
                this.note = this.template_text;
            }
            if (this.admission_enrolled_student !== ""){
                this.template_text = this.template_text.replaceAll('[admission_enrolled_student]', this.admission_enrolled_student)
                this.note = this.template_text;
            }
            if (this.circular_date !== ""){
                const circular_date = moment(this.circular_date).format("DD MMMM YYYY")
                this.template_text = this.template_text.replaceAll('[circular_date]', circular_date)
                this.note = this.template_text;
            }
        }
    },
    watch: {
        // circular_date(circular_date){
        //     circular_date = moment(circular_date).format("DD MMMM YYYY")
        //     this.template_text = this.template_text.replaceAll('[circular_date]', circular_date)
        //     this.note = this.template_text
        //     return circular_date
        // },
        note(note){
            return note
        }
    }
}
</script>

<style scoped>

</style>
