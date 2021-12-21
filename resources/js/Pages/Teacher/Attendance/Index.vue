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
                    <Back :back-url="$page.props.urlPrev" />
                </div>
                <div class="card-body">
                    <table class="table table-striped text-center">
                        <thead>
                        <tr>
                            <th>Sl#</th>
                            <th>Date</th>
                            <th>Download</th>
                            <th>D. By</th>
                            <th>D. At</th>
                        </tr>
                        </thead>
                        <tbody v-for="(m, index) in madrasah">
                        <tr class="text-center text-capitalize">
                            <th colspan="5">{{ m.name }}</th>
                        </tr>
                        <tr>
                            <td>{{ index + 1 }}</td>
                            <td>{{ today }}</td>
                            <td>
                                <InertiaLink :href="route('teacher_attendance.create', { 'date': today, 'madrasah': m.id })">
                                    Download
                                </InertiaLink>
                            </td>
                            <td>
                                {{ todayAtendanceForMad(m.id)?.creator?.name }}
                            </td>
                            <td>
                                {{ createdFromNow(todayAtendanceForMad(m.id)?.created_at) }}
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
export default {
    name: "Index",
    props: ['madrasah', 'attendanceToday'],
    components: {Back, PageHeader, Authenticated},
    data() {
      return {
          today: moment().format("Y-MM-DD")
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
        }
    }
}
</script>

<style scoped>

</style>
