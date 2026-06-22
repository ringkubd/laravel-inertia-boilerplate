<template>
    <form @submit.prevent="updateForm">
        <table class="w-full text-sm">
            <tbody>
            <tr>
                <td class="text-xs text-gray-500 pr-2">Student</td>
                <td>
                    <input type="number" name="student_amount"
                        class="w-20 text-right border border-gray-200 rounded px-1 py-0.5 text-sm focus:border-brand-300 focus:ring-1 focus:ring-brand-200"
                        v-model="student_amount">
                </td>
            </tr>
            <tr>
                <td class="text-xs text-gray-500 pr-2">Board</td>
                <td>
                    <input type="number" name="board_amount"
                        class="w-20 text-right border border-gray-200 rounded px-1 py-0.5 text-sm focus:border-brand-300 focus:ring-1 focus:ring-brand-200"
                        v-model="board_amount">
                </td>
            </tr>
            <tr>
                <td class="text-xs text-gray-500 pr-2">Institute</td>
                <td>
                    <input type="number" name="institute"
                        class="w-20 text-right border border-gray-200 rounded px-1 py-0.5 text-sm focus:border-brand-300 focus:ring-1 focus:ring-brand-200"
                        v-model="institute_amount">
                </td>
            </tr>
            <tr class="border-t border-gray-200">
                <td class="text-xs font-semibold text-gray-700 pr-2 pt-1">Total</td>
                <td class="pt-1">
                    <input type="text" disabled :value="total"
                        class="w-20 text-right font-semibold text-gray-900 bg-gray-50 rounded px-1 py-0.5 text-sm">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-2">
                    <button type="submit"
                        class="w-full text-xs font-medium text-white bg-brand-600 hover:bg-brand-700 rounded px-2 py-1 transition">
                        Update
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
export default {
    name: "InvoiceForm",
    props: ['fee', 'changeAmount'],
    data() {
        return {
            student_amount: this.fee?.student_amount ?? 0,
            board_amount: this.fee?.board_amount ?? 0,
            institute_amount: this.fee?.institute_amount ?? 0,
        }
    },
    computed: {
        total() {
            return Number(this.student_amount) + Number(this.board_amount) + Number(this.institute_amount);
        },
    },
    methods: {
        updateForm() {
            this.changeAmount({
                student_amount: this.student_amount,
                board_amount: this.board_amount,
                institute_amount: this.institute_amount,
                amount: this.total,
                invoice_details_id: this.fee.id,
            });
        },
    },
}
</script>
