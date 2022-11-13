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
                        <div class="form-group">
                            <input type="date" id="current_date" v-model="today" class="form-control" @change="chanDate">
                        </div>
                    </CardHeader>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
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
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(att, index) in attendances.data">
                            <td>{{ index + 1 }}</td>
                            <td>{{ att?.user?.name }}</td>
                            <td>{{ att?.user?.madrasah?.name }}</td>
                            <td>{{ att?.date }}</td>
                            <td>
                                {{ att.login }}
                                <vue-picture-swipe v-if="att.login_photo" :items="imageItems('/teacher_attendance/'+att.login_photo)" />
                            </td>
                            <td>
                                {{ att.logout }}
                                <vue-picture-swipe v-if="att.logout_photo" :items="imageItems('/teacher_attendance/'+att.logout_photo)" />
                            </td>
                            <td>
                                <span v-if="att.login_location">
                                    {{distance(att?.user?.madrasah?.location, att.login_location)}}M
                                </span>
                            </td>
                            <td>
                                <span v-if="att.logout_location">
                                    {{distance(att?.user?.madrasah?.location, att.logout_location)}}M
                                </span>
                            </td>
                            <td></td>
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
export default {
    name: "AppAttendance",
    components: {Paginator, Back, CardHeader, Authenticated},
    props: ['attendances'],
    data(){
        return {
            today: moment().toDate()
        }
    },
    methods: {
        chanDate(){},
        distance(madrasah_location, current_location){
            return getPreciseDistance(JSON.parse(madrasah_location), JSON.parse(current_location));
        },
        imageItems(image){
            let items = []
            const item = {
                src: image,
                thumbnail: image,
                title: 'Will be used for caption'
            };
            items.push(item)
            return items;
        }
    }
}
</script>

<style scoped>
img[itemprop="thumbnail"] {
    width: 50px;
}
</style>
