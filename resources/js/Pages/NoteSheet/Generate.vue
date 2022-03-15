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
                                    <label for="template">Template</label>
                                    <select class="form-control" name="invoice" id="template" v-model="template_id" @change="templateInfo">
                                        <option value=""></option>
                                        <option v-for="temp in note_sheet_template" :value="temp.id">{{temp.title}}</option>
                                    </select>
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
export default {
    name: "Generate",
    props: ['invoices', 'note_sheet_template', 'page_no', 'serial_no'],
    components: {Button, Back, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            invoice_id: "",
            template_id: "",
            template_text: "",
            invoice_info: {},
            note: '',
            mma_table: '',
            ad_table: '',
        }
    },
    methods: {
        invoiceInfo(){
            const invInfo = this.invoices.filter((inv) => {
                return inv.invoice_id === this.invoice_id
            })
            this.invoice_info = invInfo[0]
        },
        async templateInfo(){
            const noteST = this.note_sheet_template.filter((nst) => {
                return nst.id === this.template_id
            })
            let content = noteST[0]?.content
            const app = this;
            if (content.search('[mma_table]') !== -1){
                await axios.get(route('mma_table', this.invoice_id))
                    .then(function (d) {
                        app.mma_table = d.data
                    })
            }
            if (content.search('[ad_table]') !== -1){
                await axios.get(route('admission_table', this.invoice_id))
                    .then(function (d) {
                        app.ad_table = d.data
                    })
            }
            const numberFormat = new Intl.NumberFormat('en-BD', { style: 'currency', currency: 'BDT' })
            this.template_text = content.replace('[number_of_student]', this.invoice_info?.number_of_student)
                .replace('[amount]', numberFormat.format(this.invoice_info?.total_amount))
                .replace('[section]', this.invoice_info?.section)
                .replace('[subject]', this.invoice_info?.subject)
                .replace('[semester]', this.invoice_info?.semester)
                .replace('[bill_group]', this.invoice_info?.bill_group)
                .replace('[current_date]', this.invoice_info?.current_date)
                .replace('[invoice_date]', this.invoice_info?.invoice_date)
                .replace('[total_amount]', numberFormat.format(this.invoice_info?.total_amount))
                .replace('[idb_account]', this.invoice_info?.idb_account)
                .replace('[bill_period]', this.invoice_info?.bill_period)
                .replace('[session]', this.invoice_info?.session)
                .replace('[bill_type]', JSON.parse(this.invoice_info?.fee_type))
                .replace('[mma_table]', app.mma_table)
                .replace('[ad_table]', app.ad_table)
                .replace('<ol>', `<ol start={this.serial_no}>`)
            this.note = this.template_text

        },
        noteEdit(v){
            this.note = v
        },
        storeNote(){
            console.log(this)
            this.$inertia.post(route('note_sheet.store'), {
                note_text: this.note,
                page_no: this.page_no,
                serial_no: this.serial_no,
                invoice_id: this.invoice_id
            })
        }
    }
}
</script>

<style scoped>

</style>
