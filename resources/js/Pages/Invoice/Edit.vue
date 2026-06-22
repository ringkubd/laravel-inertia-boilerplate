<template>
    <Head>
        <title>Invoice</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Edit Invoice</page-header>
        </template>
        <div class="container-fluid py-3">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3 flex flex-wrap items-center gap-3">
                    <Back :back-url="route('invoice.index')"></Back>
                    <span class="text-sm text-gray-500 ml-auto">Invoice: <strong>{{ basicInfo.invoice_id }}</strong> | Date: {{ basicInfo.invoice_date }}</span>
                </div>
                <div class="overflow-x-auto p-4" id="printme">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Sl.#</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Trade</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Branch</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Account</th>
                                <th :colspan="feeTypes != null ? feeTypes.length * 2 : 0"
                                    class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase border-x border-gray-200">
                                    Tuition Fees
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            </tr>
                            <tr class="bg-gray-50">
                                <th :colspan="5" class="p-0"></th>
                                <th v-for="feeType in feeTypes" :key="feeType" colspan="2"
                                    class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase border-x border-gray-200">
                                    {{ feeType }}
                                </th>
                                <th class="p-0"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(invoice, index) in data" :key="invoice.id" class="hover:bg-gray-50 transition">
                                <td class="px-3 py-2 text-sm text-gray-500">{{ index + 1 }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.student_name }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.student?.polytechnic_trade_id }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.bank_branch }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.bank_account }}</td>
                                <td v-for="fee in invoice.details" :key="fee.id" colspan="2"
                                    class="px-2 py-1 border-x border-gray-100">
                                    <Form :fee="fee" :changeAmount="changeAmount"/>
                                </td>
                                <td v-if="feeTypes && invoice.details.length < feeTypes.length"
                                    :colspan="(feeTypes.length - invoice.details.length) * 2" class="px-3 py-2"></td>
                                <td class="px-3 py-2 text-sm text-gray-900 text-right font-medium">{{ invoice.amount }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-semibold">
                                <td :colspan="5 + (feeTypes != null ? feeTypes.length * 2 : 0)"
                                    class="px-3 py-2 text-sm text-gray-700 text-right">Total</td>
                                <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ totalInvoiceAmount() }}</td>
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
import Back from "@/Shared/Back";
import Form from "@/Pages/Invoice/inc/Form";
export default {
    name: "Edit",
    props: ['can', 'errors', 'data', 'feeTypes', 'basicInfo'],
    components: {Form, Back, Authenticated},
    methods: {
        totalInvoiceAmount(){
            return this.data.reduce((total, invoice) => total + Number(invoice.amount), 0);
        },
        changeAmount(formData){
            this.$inertia.put(route('invoice.update', formData.invoice_details_id), formData)
        }
    }
}
</script>
