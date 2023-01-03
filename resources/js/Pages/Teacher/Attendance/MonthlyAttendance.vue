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
                            <h6>Teacher's Attendance</h6>
                            <h5>{{madrasahs.filter((m) => request.madrasha_id == m.id)[0]?.name ?? ""}}</h5>
                            <h6>{{month_year}}</h6>
                        </div>
                        <table class="table table-bordered print:text-sm">
                            <thead class="text-center align-middle border-2 border-gray-200">
                            <tr>
                                <th>Date</th>
                                <td v-for="t in teachers" :key="t.id">{{t.name}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(attn, attnDate) in attendances" :key="attnDate" class="text-center align-middle">
                                <td class="text-sm">{{attnDate}}</td>
                                <td v-for="t in teachers" :key="t.id +''+ attnDate" class="p-0 m-0">
                                    <div class="flex flex-col">
                                        <div>
                                            {{attn.filter((a) => a.user_id === t.users_id)[0]?.status ?? 'A'}}
                                        </div>
                                        <div class="text-sm font-extralight">
                                            {{attn.filter((a) => a.user_id === t.users_id)[0]?.login ?? ''}}
                                        </div>
                                        <div class="text-sm font-extralight">
                                            {{attn.filter((a) => a.user_id === t.users_id)[0]?.logout ?? ''}}
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
    props: ['attendances', 'teachers', 'request', 'madrasahs'],
    components: {Button, CardHeader, PageHeader, Authenticated},
    data(){
        return {
            month_year: this.request?.year + "-" + this.request?.month,
            madrasah_id: this.request?.madrasha_id ?? "",
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
        }
    }
}
</script>

<style scoped>

</style>
