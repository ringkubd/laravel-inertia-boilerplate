<template>
    <Head>
        <title>Teacher Attendance Location</title>
    </Head>
<Authenticated>
    <template #header>
        <PageHeader>Teacher's Attendance</PageHeader>
    </template>
    <div class="container-fluid min-h-screen">
        <div class="card">
            <div class="card-header">
                <Back :back-url="route('app_attendance.index')" />
            </div>
            <div class="card-body">
                <MapboxMap
                    access-token="pk.eyJ1IjoiYW53YXJpZGIiLCJhIjoiY2xhNTVia282MTd4YjN4bWhuOHZnczYydyJ9.LvvEs2YhpE4xoe5FNW9oww"
                    map-style="mapbox://styles/mapbox/streets-v11"

                    :zoom="1"
                    @mb-created="(mapInstance) => map = mapInstance"
                >

                </MapboxMap>
                <MapboxMap
                    style="height: 100vh"
                    access-token="pk.eyJ1IjoiYW53YXJpZGIiLCJhIjoiY2xhNTVia282MTd4YjN4bWhuOHZnczYydyJ9.LvvEs2YhpE4xoe5FNW9oww"
                    map-style="mapbox://styles/anwaridb/cla6ot5z0002915qwrfuh74b8"
                    :center="position"
                    :zoom="16">
                    <MapboxMarker :lng-lat="position" />
                </MapboxMap>
            </div>
        </div>
    </div>

</Authenticated>
</template>

<script>
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import Authenticated from "@/Layouts/Authenticated";
import Back from "@/Shared/Back";
import {ref} from "vue";
const map = ref();
export default {
    name: "AttendanceMap",
    components: {Back, Authenticated, MapboxMap, MapboxMarker},
    props: ['attendance'],
    data(){
        return {
            position: [this.attendance.data.login_location_longitude, this.attendance.data.login_location_latitude]
        }
    }
}
</script>

<style scoped>

</style>
