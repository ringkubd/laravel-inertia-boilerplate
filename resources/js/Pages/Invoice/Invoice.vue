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
                            <h2 class="font-bold">IsDB-BISEW 4-Year Diploma Scholarship Program</h2>
                        </div>
                        <div></div>
                    </div>
                    <table class="table table-secondary border-0 print:border-0">
                        <thead class="border-0 print:border-0">
                            <tr class="border-0 print:border-0">
                                <th :colspan="5+ (feeTypes != null ? feeTypes.length : 0)" rowspan="4"
                                    class="border-0 print:border-0">
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
                                                    <th class="text-left pr-6">MMA Month & Number:</th>
                                                    <td class="text-left px-6">
                                                        {{moment(basicInfo.invoice_month).format('MMM y')}}, {{basicInfo.invoice_no + number_of_mma_till_last_semester }}/48
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
                            <tr class="align-middle border-1 print:border-1"
                                style="background-color: #e0d5d5!important;">
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
                                <th v-for="feeType in feeTypes" :key="feeType">
                                    {{feeType}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="!hasAdmiFee && !hasNullRemarks">
                                <template v-for="group in groupedInvoicesByRemarks" :key="group.key">
                                    <tr class="border-1" style="background-color: #efefef !important; font-weight: 700;">
                                        <th :colspan="8 + (feeTypes != null ? feeTypes.length : 0)" style="text-align: center!important; border: 1px solid rgb(0,0,0)!important">
                                            {{ group.heading }}
                                        </th>
                                    </tr>
                                    <tr v-for="(invoice, index) in group.rows" :key="`${group.key}-${invoice.id}`" class="border-1">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td class="text-center">{{ invoice.student?.polytechnic_roll }}</td>
                                        <td style="width: 20%!important;">{{ invoice.student_name }}</td>
                                        <td style="width: 15%!important;">{{ getFirstWord(invoice.student.polytechnic_trade_id)
                                            }}</td>
                                        <td style="width: 25%!important;">{{ invoice.bank_branch }}</td>
                                        <td>{{ invoice.bank_account }}</td>
                                        <td class="text-center" v-for="(ty, feeIndex) in feeTypes" :key="`${group.key}-${invoice.id}-${feeIndex}`">
                                            {{tuition_fees(invoice.details, ty)}}
                                        </td>
                                        <td class="text-center">{{invoice.amount}}</td>
                                        <td class="text-right">{{ group.key }}</td>
                                    </tr>
                                </template>
                            </template>
                            <template v-else>
                                <tr v-for="(invoice, index) in data" :key="invoice.id" class="border-1">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td class="text-center">{{ invoice.student?.polytechnic_roll }}</td>
                                    <td style="width: 20%!important;">{{ invoice.student_name }}</td>
                                    <td style="width: 15%!important;">{{ getFirstWord(invoice.student.polytechnic_trade_id)
                                        }}</td>
                                    <td style="width: 25%!important;">{{ invoice.bank_branch }}</td>
                                    <td>{{ invoice.bank_account }}</td>
                                    <td class="text-center" v-for="(ty, feeIndex) in feeTypes" :key="`${invoice.id}-${feeIndex}`">
                                        {{tuition_fees(invoice.details, ty)}}
                                    </td>
                                    <td class="text-center">{{invoice.amount}}</td>
                                    <td class="text-right">{{remarks(invoice.result_status, invoice.payment_slip, basicInfo)}}</td>
                                </tr>
                            </template>
                            <tr rowspan="2"
                                style="border: 1px solid rgb(0,0,0)!important; color: black!important; font-weight: 600">
                                <th :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" class="total"
                                    style="text-align: right!important; border: 1px solid rgb(0,0,0)!important;">Total
                                    Amount</th>
                                <th class="text-center">{{totalInvoiceAmount()}}</th>
                                <th class="text-center"></th>
                            </tr>
                            <tr
                                style="border: 1px solid rgb(0,0,0)!important; color: black!important; font-weight: 600">
                                <th colspan="2">In Words</th>
                                <th :colspan="6+ (feeTypes != null ? feeTypes.length : 0)" class="total"
                                    style="text-align: left!important; border: 1px solid rgb(0,0,0)!important;">{{
                                    number2wordEnglish(totalInvoiceAmount())}} Taka Only</th>
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
                                        <hr style="color: black!important;" class="w-1/2">
                                        <span>Accounts Officer</span>
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
                                 <td class="text-center" style="padding-top: 50px!important;">
                                    <div class="text-center flex flex-col justify-center items-center">
                                        <hr style="color: black!important;" class="w-1/2" />
                                        <span>Sr. Program Coordinator</span>
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
                    <div class="page-break mt-5 print:break-after-all"></div>
                    <div style="margin: 0 50px;">
                    <div style="margin-top: 1.7in"></div>

                    <div class="flex flex-col leading-5" id="bank_page" style="margin-right: 30%;">
                        <div class="flex flex-row space-x-2" style="margin-bottom: 10px!important;">
                            <div>Date:</div>
                            <div>{{ moment().format('DD MMM Y') }}</div>
                        </div>
                        <div class="flex flex-row mb-2 space-x-2">
                            <div>Ref:</div>
                            <div></div>
                        </div>
                        <div>The Manager</div>
                        <div>Islami Bank Bangladesh PLC.</div>
                        <div>Agargaon Branch</div>
                        <div>Sher-e-Bangla Nagar</div>
                        <div>Dhaka-1207</div>
                        <div class="flex flex-col my-2">
                            <div class="mb-3">Dear Sir,</div>
                            <div class="text-justify w-full leading-relaxed">
                                You are requested to kindly transfer the amount as mentioned below against the name of
                                the student to his/her personal account with you from the current A/C no.
                                20502240100000115 of IsDB-BISEW.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center justify-content-center"
                        style="flex: auto; justify-content: center; align-items: center; width: 100%">
                        <table class="table table-auto bank_sheet"
                            style="border: 1px solid rgb(0,0,0)!important; color: black!important;width: 100%">
                            <thead class="border-1 print:border-1 thead">
                                <tr class="align-middle border-1 print:border-1 thead"
                                    style="background-color: #e0d5d5!important;">
                                    <th>Sl.#</th>
                                    <th>Name</th>
                                    <th>IBBL Branch</th>
                                    <th>IBBL Account</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(invoice, index) in data.filter(i => i.amount > 0)" :key="invoice.id"
                                    :class="'border-1'"
                                    :style="(index % 25 === 0 ? ' margin-top: 1.7in !important': ' table-row')">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ invoice.student_name }}</td>
                                    <td>{{ invoice.bank_branch }}</td>
                                    <td class="text-center">{{ invoice.bank_account }}</td>
                                    <td class="text-center">{{parseInt(invoice.amount).toLocaleString('en-BD', {
                                        maximumFractionDigits: 2
                                        })}}</td>
                                </tr>
                                <tr
                                    style="border: 1px solid rgb(0,0,0)!important; color: black!important; font-weight: 600">
                                    <th :colspan="4" class="total"
                                        style="text-align: center!important; border: 1px solid rgb(0,0,0)!important;">
                                        Total Amount</th>
                                    <th class="text-center">{{totalInvoiceAmount()}}</th>
                                </tr>
                                <tr
                                    style="border: 1px solid rgb(0,0,0)!important; color: black!important; font-weight: 600">
                                    <th :colspan="5" class="total"
                                        style="text-align: center!important; border: 1px solid rgb(0,0,0)!important;">In
                                        Words (Taka): {{ number2wordEnglish(totalInvoiceAmount())}} Only</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-col leading-6" style="padding-top: 50px!important;">
                        <div class="flex flex-row justify-between">
                            <div class="flex flex-col">
                                <div class="font-bold signature">Md Faijul Islam</div>
                                <div>Accounts Officer</div>
                            </div>
                            <div class="flex flex-col">
                                <div class="font-bold signature">Neaz Khan</div>
                                <div>Chief Executive Officer</div>
                            </div>
                        </div>
                    </div>
                    </div>
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
import number2wordEnglish from "number2english_word";
export default {
    name: "Invoice",
    computed: {
        moment() {
            return moment
        },
        hasAdmiFee() {
            const invoiceHasAdmi = this.data.some((invoice) => {
                return invoice.details?.some((detail) => detail.fee_type === 'Admi. Fee');
            });
            const feeTypeListHasAdmi = this.feeTypes?.includes('Admi. Fee');
            return invoiceHasAdmi || feeTypeListHasAdmi;
        },
        hasNullRemarks() {
            return this.data.some((invoice) => {
                const remark = this.remarks(invoice.result_status, invoice.payment_slip, this.basicInfo);
                return remark === null || remark === undefined || remark === '';
            });
        },
        groupedInvoicesByRemarks() {
            if (this.hasAdmiFee || this.hasNullRemarks) {
                return [];
            }

            const grouped = this.data.reduce((acc, invoice) => {
                const remark = this.remarks(invoice.result_status, invoice.payment_slip, this.basicInfo);
                if (!acc[remark]) {
                    acc[remark] = [];
                }
                acc[remark].push(invoice);
                return acc;
            }, {});

            const orderedRemarks = ['Passed', 'DS', 'DNS', 'Referred', 'Dropout'];
            const knownGroups = orderedRemarks
                .filter((remark) => grouped[remark]?.length)
                .map((remark) => ({
                    key: remark,
                    heading: this.remarkHeading(remark),
                    rows: grouped[remark],
                }));

            const otherGroups = Object.keys(grouped)
                .filter((remark) => !orderedRemarks.includes(remark))
                .sort()
                .map((remark) => ({
                    key: remark,
                    heading: this.remarkHeading(remark),
                    rows: grouped[remark],
                }));

            return [...knownGroups, ...otherGroups];
        },
    },
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo', 'last_mma', 'number_of_mma_till_last_semester'],
    components: {PageHeader, Button, Back, CardHeader, Authenticated},
    methods: {
        number2wordEnglish,
        search(params){

        },
        totalInvoiceAmount(){
            let total = 0;
            const self = this
            this.data.map(function (invoice){
                total += invoice.amount
            })
            return parseInt(total).toLocaleString('en-BD', {
                maximumFractionDigits: 2
            });
        },
        print() {
            // const element = this.$el.querySelector('.table-page-break');
            // if (element) {
            //     // Force reflow
            //     element.offsetHeight;
            // }
            console.log(this.$el)
            const origin = window.location.origin;
            const printOptions = {
                name: '_blank',
                specs: [
                    'fullscreen=yes',
                    'titlebar=yes',
                    'scrollbars=yes'
                ],
                styles: [
                    `${origin}/css/custom_print.css`,
                    `${origin}/css/app.css`
                ]
            };

            // Sometimes the print window prints before linked styles load.
            // Use the plugin but wait briefly to allow stylesheet fetch.
            this.$htmlToPaper('printme', printOptions, (e) => {
                // plugin callback may fire before styles applied; add a small delay
                setTimeout(() => {
                    console.log('printed', this);
                }, 250);
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
        remarkHeading(remark) {
            if (remark === 'DS') {
                return 'Document Submitted (DS)';
            }
            if (remark === 'DNS') {
                return 'Document Not Submitted (DNS)';
            }
            if (remark === 'Dropout') {
                return 'Dropout';
            }
            return `${remark}`;
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
.table-page-break {
    page-break-before: always;margin-top: 1.7in; /* Add 1.7in blank space for letterhead */
}

/* Print / A4 layout tweaks */
@page {
    size: A4;
    margin: 25mm 25mm 15mm 25mm; /* top right bottom left - increased side margins */
}

@media print {
    html, body {
        /* let @page margins control the printable area; avoid forcing full-page width */
        width: auto;
        height: auto;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Container that will be printed */
    #printme {
        box-sizing: border-box;
        /* subtract left+right page margins (25mm + 25mm = 50mm) from full A4 width */
        max-width: calc(210mm - 50mm);
        width: 100%;
        margin: 0 auto;
        padding-top: 25mm; /* space for standard pad letterhead */
        padding-bottom: 15mm;
        padding-left: 0;
        padding-right: 0;
        background: transparent !important;
        color: #000 !important;
    }

    /* Remove shadows and unnecessary spacing on print */
    .container-fluid, .card, .card-body {
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* Tables should fit the page and avoid breaking rows across pages */
    table, .table, .bank_sheet {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: collapse;
        color: #000 !important;
    }

    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }

    tr, td, th {
        page-break-inside: avoid;
        -webkit-hyphens: none;
        hyphens: none;
    }

    /* Ensure our manual page-break helper still works */
    .page-break, .page-break-after-all { page-break-after: always; }

    /* Reduce non-printable UI */
    .btn, .mma, .no-print { display: none !important; }

    /* Fix bank page width during print */
    #bank_page {
        margin-right: 0 !important;
    }
}
</style>
