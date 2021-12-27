<template>
    <Head>
        <title>Teacher attendance sheet for {{today}}</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Teacher's Attendance Sheet</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <CardHeader :can="can">
                        <template #first>
                            <Back :back-url="route('teacher_attendance.index')"></Back>
                        </template>
                        <template #second>
                            <Button class="btn btn-success" @click="print">Print</Button>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body min-h-screen h-100" id="printme">
                    <div class="flex justify-content-center items-center text-center h-30 content-header">
                        <div>
                            <div class="isdb-header">
                                <h2>IsDB-BISEW Madrasah Program</h2>
                                <h4>Teaching Staff Attendance Sheet</h4>
                            </div>

                            <table class="table table-borderless header-table text-left" border="0">
                                <tbody>
                                <tr>
                                    <th>Name of Madrasah</th>
                                    <td>: {{ madrasah?.name }}</td>
                                </tr>
                                <tr>
                                    <th>Name of Trades</th>
                                    <td>: {{trades}}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>: {{ today }}</td>
                                </tr>
                                <tr>
                                    <th>Printing Time</th>
                                    <td>: {{ time }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive content-body">
                        <table class="table h-60">
                            <thead>
                            <tr class="text-center d-flex">
                                <th class="col-1">Sl#</th>
                                <th class="col-5">Name</th>
                                <th class="col-2">Time</th>
                                <th class="col-4">Signature</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="d-flex" v-for="(t, index) in teacher">
                                <td class="col-1">{{ index+1 }}</td>
                                <td class="col-5">{{ t?.name }}</td>
                                <td class="col-2"></td>
                                <td class="col-4"></td>
                            </tr>
                            </tbody>
                        </table>
                        <div style="height: 100px; width: 100%;" class="comments">
                            <span>Comment's of Super (if any):</span>
                        </div>
                    </div>
                    <div class="row h-20 content-footer">
                        <div class="col-4">
                            <img :src="qr_code" width="100" class="img-thumbnail"/>
                        </div>
                        <div class="col-6 offset-2 text-center">
                            <p style="margin-top: 42px;">
                            <hr />
                            <h4>Super</h4>
                            Seal & Signature
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import moment from "moment";
import PageHeader from "@/Shared/PageHeader";
import Back from "@/Shared/Back";
import CardHeader from "@/Shared/CardHeader";
export default {
    name: "AttendanceSheet",
    props: ['teacher', 'qr_code', 'madrasah', 'trades'],
    components: {CardHeader, Back, PageHeader, Authenticated},
    data(){
        return {
            today: moment().format('Y-MM-DD'),
            time: moment().format('hh-mm-ss a')
        }
    },
    methods: {
        print: function() {

            const printOptions = {
                name: '_blank',
                specs: [
                    'fullscreen=yes',
                    'titlebar=yes',
                    'scrollbars=yes'
                ],
                styles: [
                    '/css/attendance_print.css',
                ]
            }

            this.$htmlToPaper('printme', printOptions, () => {
                console.log('Printing finished');
            });
        }
    }
}
</script>

<style scoped>

</style>
