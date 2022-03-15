<template>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Number of Students</th>
            <th>Payment Against</th>
            <th>Taka / Month</th>
            <th>Period</th>
            <th>Sub. Total (Tk.)</th>
            <th>Total (TK.)</th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        <tr>
            <td>
                {{ invoice.mma_student }}
            </td>
            <th>MMA</th>
            <td>{{ invoice.student_amount }}</td>
            <td rowspan="2">{{ invoice.invoice_month }}</td>
            <td>{{ invoice.total_student_amount }}</td>
            <td rowspan="2">{{ invoice.amount }}</td>
        </tr>
        <tr>
            <td>
                {{ invoice.book_student }}
            </td>
            <td>Book & Stationary</td>
            <td>{{ invoice.board_amount }}</td>
            <td>{{ invoice.total_board_amount }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "MmaTable",
    props: ['invoice']

}
</script>

<style scoped>

</style>
