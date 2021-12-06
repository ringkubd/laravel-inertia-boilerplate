<template>
    <Head>
        <title>Student Profile | IsDB-BISEW</title>
    </Head>
    <Authenticated>
        <div class="container-fluid mt-2 min-vh-100">
            <img class="m-auto img-thumbnail mb-2 w-1/12" v-if="student.photo !== null" :src="'/'+student.photo" :alt="student.name">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="hover:text-green-900 shadow-lg border-b-2 border-gray-500">Personal Information</h2>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive">
                                <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ student.name }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ student.dob }}</td>
                                </tr>
                                <tr>
                                    <th>Nid</th>
                                    <td>{{ student.nid }}</td>
                                </tr>
                                <tr class="border-b-2 border-gray-400">
                                    <th class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500" colspan="2">Contact Information</th>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ student?.users?.email }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile 1</th>
                                    <td>{{ student.mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile 2</th>
                                    <td>{{ student.mobile_1 }}</td>
                                </tr>
                                <tr>
                                    <th>Present Address</th>
                                    <td>{{ student.present_address }}</td>
                                </tr>
                                <tr>
                                    <th>Permanent Address</th>
                                    <td>{{ student.permanent_address }}</td>
                                </tr>
                                <tr class="border-b-2 border-gray-400">
                                    <th class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500"  colspan="2">Guardian Information</th>
                                </tr>
                                <tr>
                                    <th>Father's Name</th>
                                    <td>{{ student.father_name }}</td>
                                </tr>
                                <tr>
                                    <th>Mother's Name</th>
                                    <td>{{ student.mother_name }}</td>
                                </tr>

                                <tr>
                                    <th>Guardian Mobile</th>
                                    <td>{{ student.guardian_mobile }}</td>
                                </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="hover:text-green-900 shadow-lg border-b-2 border-gray-500">Academic Information (Dakhil)</h2>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive">
                                <tbody>
                                <tr>
                                    <th>Madrasah Name</th>
                                    <td>{{ student?.madrasha?.name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ student.madrasa_completed === 1 ? "Completed" : "Continue" }}</td>
                                </tr>
                                <tr>
                                    <th>Trade</th>
                                    <td>{{ student.madrasa_trade_id }}</td>
                                </tr>
                                <tr>
                                    <th>Session</th>
                                    <td>{{ student.ssc_session }}</td>
                                </tr>
                                <tr>
                                    <th>Dakhil Roll</th>
                                    <td>{{ student.ssc_roll }}</td>
                                </tr>
                                <tr>
                                    <th>Dakhil Registration</th>
                                    <td>{{ student.ssc_registration }}</td>
                                </tr>
                                <tr class="border-b-2 border-gray-400">
                                    <th class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500" colspan="2">Result</th>
                                </tr>
                                <tr>
                                    <th>Nine CGPA</th>
                                    <td>{{ student?.madrasah_result?.nine_gpa }}</td>
                                </tr>
                                <tr>
                                    <th>Ten CGPA</th>
                                    <td>{{ student?.madrasah_result?.ten_gpa }}</td>
                                </tr>
                                <tr>
                                    <th>Pass Year</th>
                                    <td>{{ student?.madrasah_result?.pass_year }}</td>
                                </tr>
                                <tr>
                                    <th>Result Added By</th>
                                    <td>{{ student?.madrasah_result?.added_by?.name }} ({{student?.madrasah_result?.added_by?.roles[0]?.name}})</td>
                                </tr>
                                <tr>
                                    <th>Result Approved By</th>
                                    <td>{{ student?.madrasah_result?.approved_by?.name }} ({{student?.madrasah_result?.approved_by?.roles[0]?.name}})</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" v-if="student.madrasa_completed ===1 && student.polytechnic !== null">
                    <div class="card">
                        <div class="card-header text-center">
                            <h2 class="hover:text-green-900 shadow-lg border-b-2 border-gray-500">Academic Information (Diploma)</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-responsive">
                                        <tbody>
                                        <tr class="border-b-2 border-gray-400">
                                            <th  class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500" colspan="2">Academic Information</th>
                                        </tr>
                                        <tr>
                                            <th>Trade</th>
                                            <td>{{ student.polytechnic_trade_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Session</th>
                                            <td>{{ student.polytechnic_session }}</td>
                                        </tr>
                                        <tr>
                                            <th>Semister</th>
                                            <td>{{ student.semester }}</td>
                                        </tr>
                                        <tr>
                                            <th>Registration</th>
                                            <td>{{ student.polytechnic_registration }}</td>
                                        </tr>
                                        <tr>
                                            <th>Roll</th>
                                            <td>{{ student.polytechnic_roll }}</td>
                                        </tr>
                                        <tr class="border-b-2 border-gray-400">
                                            <th  class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500" colspan="2">Institute Information</th>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ student?.polytechnic?.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Institute Number</th>
                                            <td>{{ student?.polytechnic?.institution_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ student?.polytechnic?.email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact Number</th>
                                            <td>{{ student?.polytechnic?.contact_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ student?.polytechnic?.address }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr class="border-b-2 border-gray-400">
                                            <th class="text-center font-extrabold text-black-400 hover:text-green-900 shadow-lg border-b-2 border-gray-500" colspan="7">Results</th>
                                        </tr>
                                        <tr>
                                            <th>Sem.</th>
                                            <th>GPA</th>
                                            <th>Status</th>
                                            <th>F. Sub.</th>
                                            <th>Attach.</th>
                                            <th>Added By</th>
                                            <th>App. By</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="result in student.polytechnic_result">
                                            <td>{{ result.semester }}</td>
                                            <td>{{ result.gpa }}</td>
                                            <td>{{ result.status }}</td>
                                            <td>{{ result.failed_in_subject }}</td>
                                            <td class="w-1/6">
                                                <img v-if="result?.attachments?.length > 0" v-for="attachment in result?.attachments" :src="'/'+attachment.attachment" alt="" class="img-thumbnail w-1/5">
                                            </td>
                                            <td>{{ result?.added_by?.name }} ({{result?.added_by?.roles[0]?.name}})</td>
                                            <td>{{ result?.approved_by?.name }} ({{result?.approved_by?.roles[0]?.name}})</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
export default {
    name: "Profile",
    props: ['student'],
    components: {Authenticated},
    mounted(){
        console.log(this.student)
    }
}
</script>

<style scoped>
.table > :not(caption) > * > * {
    padding: 0.1rem 0.1rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: 0px;
    box-shadow: inset 0 0 0 9999px
    var(--bs-table-accent-bg);
}
</style>
