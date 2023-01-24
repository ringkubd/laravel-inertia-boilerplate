<template>
    <Head>
        <title>Monthly Attendance</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Monthly Attendance</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid min-h-screen">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <select class="form-control" v-model="month_year" @change="changeMonth">
                                    <option v-for="m in last12months()" :value="m">{{m}}</option>
                                </select>
                            </template>
                            <template #second>
                                <select class="form-control" v-model="madrasah_id" @change="changeMonth">
                                    <option v-for="m in madrasahs" :value="m.id">{{m.name}}</option>
                                </select>
                            </template>
                            <template #third>
                                <div class="flex items-center justify-center min-h-[100%]">
                                    <Button v-print="'#printMe'" class="bg-violet-500 hover:bg-violet-800">
                                        Print
                                    </Button>
                                </div>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-header table-responsive"  id="printMe">
                        <div class="flex flex-col justify-center items-center my-2">
                            <img src="/isdb-bisew.png" alt="isdb-bisew.org">
                            <h6>Teaching Staff Attendance</h6>
                            <h5>{{madrasahs.filter((m) => request.madrasha_id == m.id)[0]?.name ?? ""}}</h5>
                            <h5>{{today}}</h5>
                        </div>
                        <table class="table border print:text-sm">
                            <thead class="text-center align-middle border-2 border-gray-300">
                            <tr>
                                <th rowspan="2" class="border-r-2 border-gray-300 text-left">Name</th>
                                <th :colspan="Object.keys(dates).length"  class="border-b-2 border-gray-300">{{monthFormat()}}</th>
                            </tr>
                            <tr>
                                <td v-for="t in dates" :key="t" class="border-r-2 border-b-2 border-gray-300">{{day(t)}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(attn, teacher_id) in attendances" :key="teacher_id" class="text-center align-middle">
                                <td class="text-sm text-left border-r-2">
                                    <div class="flex flex-col">
                                        <div>
                                            {{teachers.filter((t) => t.users_id == teacher_id)[0]?.name}}
                                        </div>
                                        <div class="font-extralight text-sm">
                                            {{teachers.filter((t) => t.users_id == teacher_id)[0]?.trade?.name}}
                                        </div>
                                    </div>
                                </td>
                                <td v-for="t in dates" :key="t.id +''+ teacher_id" class="p-0 m-0 border-r-2">
                                    <div class="flex flex-col">
                                        <div>
                                            {{attn.filter((a) => a.date === t)[0]?.status ?? 'A'}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div>
                            <span>Note: </span>
                            NL- No logout, P- Present, A- Absent
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Shared/PageHeader.vue";
import CardHeader from "@/Shared/CardHeader.vue";
import moment from "moment";
import Button from "@/Shared/Button.vue";

export default {
    name: "MonthlyAttendance",
    props: ['attendances', 'teachers', 'request', 'madrasahs', 'dates'],
    components: {Button, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            month_year: this.request?.year + "-" + this.request?.month,
            madrasah_id: this.request?.madrasha_id ?? "",
            today: moment().format("hh:mm:ss - DD MMM YYYY")
        }
    },
    methods: {
        last12months(){
            const months = [];
            for (let i=0; i <= 11; i++){
                months.push(moment().subtract(i, 'month').format('YYYY-MM'))
            }
            return months;
        },
        changeMonth(){
            const yearMonth = this.month_year.split("-")
            this.$inertia.replace(route('monthly_attendance', {year: yearMonth[0], month: yearMonth[1], madrasha_id: this.madrasah_id}))
        },
        day(d){
            return moment(d).date()
        },
        monthFormat(){
            if (this.month_year){
                const dateMonth = this.month_year + "-01";
                return moment(dateMonth).format("MMMM YYYY")
            }
            return moment().format("h YYYY")
        }
    }
}
</script>

<style scoped>

</style>
