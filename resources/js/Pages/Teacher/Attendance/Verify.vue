<template>
    <Head>
        <title>Verification Page Teacher Attendance</title>
    </Head>

    <Guest>
        <template #default>
            <div class="container-fluid">
                <div class="card min-h-screen">
                    <div class="card-header text-center" v-if="verification">
                        <h2 v-if="verification">Attendance Sheet Verification Information</h2>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered" v-if="verification">
                            <tbody>
                            <tr>
                                <th>Madrasah Name</th>
                                <td>{{ verification?.madrasah?.name }}</td>
                            </tr>
                            <tr>
                                <th>Downloader Name</th>
                                <td>{{ verification?.creator?.name }}</td>
                            </tr>
                            <tr>
                                <th>Downloaded at</th>
                                <td>{{ downloadedDateTime(verification?.created_at) }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-else>
                            <h2 class="text-center text-red-600">Invalid attendance sheet. This QRCode not generated from this system.</h2>
                        </div>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </template>
    </Guest>
</template>

<script>
import Guest from "@/Layouts/Guest";
import moment from "moment";
export default {
    name: "verify",
    props: ['verification'],
    components: {Guest},
    methods: {
        downloadedDateTime(datetimestr){
            return moment(datetimestr).format('DD/MM/Y - hh:mm:ss A')
        }
    }
}
</script>

<style scoped>

</style>
