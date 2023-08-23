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
                        <template #second>
                            <div class="form-group">
                                <input type="date" id="current_date" v-model="today" class="form-control" @change="chanDate">
                            </div>
                        </template>
                        <template #third>
                            <InertiaLink :href="route('monthly_attendance')" class="no-underline justify-center items-end">
                                <Button class="bg-blend-color bg-primary">Monthly</Button>
                            </InertiaLink>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Sl#</th>
                            <th>Name</th>
                            <th>Madrasah</th>
                            <th>Date</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Login Distance</th>
                            <th>Logout Distance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(att, index) in attendances.data">
                            <td>{{ index + 1 }}</td>
                            <td>{{ att?.user?.name }}</td>
                            <td>{{ att?.user?.madrasah?.name }}</td>
                            <td>{{ att.attendanceLogOneDay?.date }}</td>
                            <td>
                                <div class="flex flex-row justify-center items-center">
                                    {{ att.attendanceLogOneDay?.login }}
                                    <vue-picture-swipe v-if="att.attendanceLogOneDay && att.attendanceLogOneDay?.login_photo" :items="imageItems('/teacher_attendance/'+att.attendanceLogOneDay?.login_photo)" />
                                </div>

                            </td>
                            <td>
                                <div class="flex flex-row justify-center items-center">
                                    {{ att.attendanceLogOneDay?.logout }}
                                    <vue-picture-swipe v-if="att.attendanceLogOneDay && att.attendanceLogOneDay?.logout_photo" :items="imageItems('/teacher_attendance/'+att.attendanceLogOneDay?.logout_photo)" />
                                </div>
                            </td>
                            <td>
                                <span v-if="att.attendanceLogOneDay && att.attendanceLogOneDay?.login_location">
                                    {{distance(att?.user?.madrasah?.location, att.attendanceLogOneDay.login_location)}}M
                                </span>
                            </td>
                            <td>
                                <span v-if="att.attendanceLogOneDay && att.attendanceLogOneDay.logout_location">
                                    {{distance(att?.user?.madrasah?.location, att.attendanceLogOneDay.logout_location)}}M
                                </span>
                            </td>
                            <th>
                                <span v-if="!att.attendanceLogOneDay">Absent</span>
                                <span v-else-if="att.attendanceLogOneDay && !att.attendanceLogOneDay.logout">No Logout</span>
                                <span v-else>Present</span>
                            </th>
                            <td>
                                <div class="flex space-x-7">
                                    <InertiaLink v-if="att.attendanceLogOneDay" :href="route('app_attendance.show', att.attendanceLogOneDay?.id)">Login Loc.</InertiaLink>
                                    <InertiaLink v-if="att.attendanceLogOneDay && att.attendanceLogOneDay.logout" :href="route('logoutLocation', att.attendanceLogOneDay?.id)">Logout Loc.</InertiaLink>
                                </div>
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
import CardHeader from "@/Shared/CardHeader";
import Back from "@/Shared/Back";
import moment from "moment";
import { getPreciseDistance } from 'geolib';
import Paginator from "@/Components/Paginator";
import Button from "@/Shared/Button.vue";
export default {
    name: "AppAttendance",
    components: {Button, Paginator, Back, CardHeader, Authenticated},
    props: ['attendances'],
    data(){
        return {
            today: moment().toDate()
        }
    },
    methods: {
        chanDate(){
            this.$inertia.replace(route('app_attendance.index', {today: this.today}))
        },
        distance(madrasah_location, current_location){
            console.log(madrasah_location, current_location)
            if (!madrasah_location || !current_location) return 'Invalid ';
            //return getPreciseDistance(JSON.parse(madrasah_location), JSON.parse(current_location));
        },
        imageItems(image){
            let items = []
            const item = {
                src: image,
                thumbnail: image,
                title: 'Will be used for caption',
                w: 300,
                h: 300
            };
            items.push(item)
            return items;
        }
    }
}
</script>

<style>
img[itemprop="thumbnail"] {
    width: 50px!important;
}
</style>
