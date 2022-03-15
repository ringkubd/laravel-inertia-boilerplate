<template>
    <form action="" @submit.prevent="updateForm" :id="'updateForm'+fee.id">
        <table>
            <tbody>
            <tr>
                <th>Student</th>
                <td class="text-right">
                    <input type="text" name="student_amount" class="max-h-2 max-w-2xl border-0 text-right w-24" v-model="student_amount">
                </td>
            </tr>
            <tr>
                <th>Board</th>
                <td class="text-right">
                    <input type="text" name="board_amount" class="max-h-2 max-w-2xl border-0 text-right w-24" v-model="board_amount">
                </td>
            </tr>
            <tr>
                <th>Institute</th>
                <td class="text-right">
                    <input type="text" name="institute" class="max-h-2 max-w-2xl border-0 text-right w-24" v-model="institute_amount">
                </td>
            </tr>
            <tr class="border-t-2">
                <th>Total</th>
                <td class="text-right">
                    <input type="text" disabled v-model="amount" class="max-h-2 max-w-2xl border-0 text-right w-24">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-right">
                    <input type="submit" value="Update" class="border-1 bg-green-600 rounded">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
export default {
    name: "Form",
    props: ['fee', 'changeAmount'],
    data(){
        return {
            student_amount: this.fee.student_amount,
            board_amount: this.fee.board_amount,
            institute_amount: this.fee.institute_amount,
            amount: this.fee.amount
        }
    },
    methods: {

    },
    computed: {
        amount(){
            this.amount =  parseInt(this.student_amount) + parseInt(this.board_amount) + parseInt(this.institute_amount)
            return this.amount;
        },
        updateForm(){
            this.changeAmount({
                student_amount: this.student_amount,
                board_amount: this.board_amount,
                institute_amount: this.institute_amount,
                amount: this.amount,
                invoice_details_id: this.fee.id
            })
            console.log(this.amount)
        }
    },
    mounted() {
        // console.log(this.fee)
    }
}
</script>

<style scoped>

</style>
