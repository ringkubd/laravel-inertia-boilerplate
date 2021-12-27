<template>
    <Head>
        <title>Teacher's Attendance</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Teacher's Attendance</PageHeader>
        </template>
        <div class="container-fluid min-h-screen">
            <div class="card">
                <div class="card-header">
                    <CardHeader>
                        <template #first>
                            <Back :back-url="$page.props.urlPrev" />
                        </template>
                        <template #second v-if="permission">
                            <div class="form-group">
                                <input type="date" id="current_date" v-model="today" class="form-control" @change="chanDate">
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body">
                    <table class="table table-striped text-center">
                        <thead>
                        <tr>
                            <th>Sl#</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Download</th>
                            <th>D. By</th>
                            <th>D. At</th>
                        </tr>
                        </thead>
                        <tbody v-for="(m, index) in madrasah">
                        <tr>
                            <td>{{ index + 1 }}</td>
                            <th class="text-left">{{ m.name }}</th>
                            <td>{{ today }}</td>
                            <td>
                                <InertiaLink :href="route('teacher_attendance.create', { 'date': today, 'madrasah': m.id })" v-if="current_date == today || m?.teacher_attendance?.length > 0">
                                    Download
                                </InertiaLink>
                            </td>
                            <td>
                                {{ m?.teacher_attendance[0]?.creator?.name }}
                            </td>
                            <td>
                                {{ createdFromNow(m?.teacher_attendance[0]?.created_at) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Back from "@/Shared/Back";
import moment from "moment";
import CardHeader from "@/Shared/CardHeader";
import Label from "@/Components/Label";
export default {
    name: "Index",
    props: ['madrasah', 'attendanceToday', 'permission'],
    components: {Label, CardHeader, Back, PageHeader, Authenticated},
    data() {
      return {
          today: GET('date')[0] ?? moment().format("Y-MM-DD"),
          current_date: moment().format("Y-MM-DD")
      }
    },
    methods: {
        getDaysArrayByMonth() {
            let daysInMonth = moment().daysInMonth();
            const arrDays = [];

            while(daysInMonth) {
                const current = moment().date(daysInMonth);
                arrDays.push(current);
                daysInMonth--;
            }

            return arrDays;
        },
        todayAtendanceForMad(madId){
            return this.attendanceToday.filter((attn) => {
                return attn.madrasha_id === madId
            })
        },
        createdFromNow(timeString){
            if(timeString == undefined || timeString == "") return ""
            return moment(timeString).fromNow()
        },
        chanDate(){
            this.$inertia.replace(route('teacher_attendance.index', { date: this.today }))
        }
    }
}
</script>

<style scoped>

</style>
