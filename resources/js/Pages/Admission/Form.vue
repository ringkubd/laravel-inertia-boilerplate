<template>
    <div>
        <form action="" enctype="multipart/form-data" @submit.prevent="submitForm">
            <fieldset class="form-group mx-2 px-3 group hover:shadow-inner shadow-2xl border-blue-400" v-if="createForm">
                <legend>Student</legend>
                <div class="row">
                    <label class="col-3" for="student">Select a Student</label>
                    <Multiselect
                        :autofocus="true"
                        placeholder="Choose a student."
                        id="student"
                        :filterResults="true"
                        :minChars="1"
                        :resolveOnLoad="true"
                        :delay="0"
                        :searchable="true"
                        limit="50"
                        class="ml-4"
                        @select="onSelectStudent"
                        v-model="formData.student_id"
                        :options="async function(query) {
                                                return await fetchStudents(query)
                                              }"
                    />
                    <div class="text-red-500" v-if="errors.student_id">
                        {{ errors.student_id }}
                    </div>
                </div>
            </fieldset>
            <div class="flex justify-between flex-grow pb-4">
                <div class="flex-1 px-3 mx-2 shadow-lg items-center">
                    <h2 class="hover:italic hover:text-green-900">Profile</h2>
                    <table class="table table-auto table-hover transition ease-in duration-700" v-if="studentData">
                        <tbody>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Photo</th>
                            <td>
                                <vue-picture-swipe :items="imageItems(studentData?.photo)" />
                            </td>
                        </tr>
                        <tr>
                            <th class="w-1/3">Name</th>
                            <td>{{studentData?.name}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Madrasah</th>
                            <td>{{studentData?.madrasha?.name}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Madrash Trade</th>
                            <td>{{studentData?.madrasa_trade_id}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Dakhil Roll</th>
                            <td>{{studentData?.ssc_roll}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Dakhil Registration</th>
                            <td>{{studentData?.ssc_registration}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl">
                            <th class="w-1/3">Dakhil Session</th>
                            <td>{{studentData?.ssc_session}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 class="hover:italic hover:text-green-900">Result</h2>
                    <table class="table table-auto table-hover transition ease-in duration-700" v-if="studentData">
                        <thead>
                        <tr class="hover:shadow-2xl">
                            <th>Exam Name</th>
                            <th>Result</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="hover:shadow-2xl group">
                            <td class="group-hover:font-bold group-hover:transform scale-100 ease-in-out">Nine</td>
                            <td>{{studentData?.madrasah_result?.nine_gpa}}</td>
                            <td>{{studentData?.madrasah_result?.nine_gpa ? "Pass" : ""}}</td>
                        </tr>
                        <tr class="hover:shadow-2xl group">
                            <td class="group-hover:font-bold group-hover:transform scale-100 ease-in-out">Dakhil</td>
                            <td>{{studentData?.madrasah_result?.ten_gpa}}</td>
                            <td>{{studentData?.madrasah_result?.status}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex-1 px-3 mx-2 shadow-lg items-center">
                    <h2 class="hover:italic hover:text-green-900">Application Information</h2>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label for="polytechnic" class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Polytechnic</label>
                        <Multiselect
                            :autofocus="true"
                            placeholder="Choose Polytechnic."
                            id="polytechnic"
                            :filterResults="true"
                            :minChars="1"
                            :resolveOnLoad="true"
                            :delay="0"
                            :searchable="true"
                            limit="50"
                            mode="multiple"
                            class="border border-transparent"
                            v-model="formData.polytechnics"
                            :options="polytechnic"
                        />
                        <div class="text-red-500" v-if="errors.student_id">
                            {{ errors.student_id }}
                        </div>
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label for="session" class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Session</label>
                        <Multiselect
                            :autofocus="true"
                            placeholder="Choose Session."
                            id="session"
                            :filterResults="false"
                            :minChars="1"
                            :resolveOnLoad="true"
                            :delay="0"
                            :searchable="true"
                            mode="single"
                            limit="50"
                            v-model="formData.academic_session"
                            class="border border-transparent"
                            :options="sessions"
                        />
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Trade Choice</label>
                        <Multiselect
                            :autofocus="true"
                            placeholder="Choose Trades."
                            id="trades"
                            :filterResults="true"
                            :minChars="1"
                            :resolveOnLoad="true"
                            :delay="0"
                            :searchable="true"
                            mode="multiple"
                            v-model="formData.trades"
                            limit="50"
                            class="ml-4"
                            :options="trades"
                        />
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Tracking ID</label>
                        <input type="text" class="form-control" v-model="formData.tracking_id" name="tracking_id">
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Admission Fee</label>
                        <input type="number" class="form-control" v-model="formData.admission_fee" name="admission_fee">
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <label class="group-hover:animate-pulse group-hover:text-blue-500 group-hover:font-bold">Payment Prove</label>
                        <input type="file" class="form-control" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/*" name="money_receipt">
                    </div>
                    <div class="form-group md:mb-3 group hover:shadow-inner shadow-2xl border-blue-400">
                        <div class="flex justify-end align-items-end">
                            <input type="submit" class="btn btn-success rounded transition transform hover:-translate-y-1 motion-reduce:transition-none motion-reduce:transform-none" v-if="createForm" value="Apply">
                            <input type="submit" class="btn btn-warning rounded" v-else value="Update">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Multiselect from '@vueform/multiselect'
export default {
    name: "Form",
    props: ['can', 'students', 'createForm', 'errors', 'createForm', 'trades', 'sessions', 'polytechnic'],
    components: {
        Authenticated,
        Multiselect
    },
    mounted(){
        console.log(this.trades)
    },
    data() {
        return {
            formData: {
                student_id: "",
                trades: [],
                polytechnics: [],
                academic_session: "",
                tracking_id: "",
                admission_fee: "",
                money_receipt: ''
            },
            studentData: false
        }
    },
    methods: {
        async fetchStudents(query){
            let where = ''

            if (query) {
                where = {'name': query}
            }
            const response = await fetch(route('admission_student_list', where))

            const data = await response.json();
            return data;
        },
        submitForm(){

        },
        async onSelectStudent(student_id){
            const response = await fetch(route('admission_student_profile', student_id))
                .then(res => res.json())
            this.studentData = response
        },
        imageItems(image){
            let items = []
            if (image !== null){
                var item = {
                    src: '/'+image,
                    thumbnail: '/'+image,
                    w: 1200,
                    h: 900,
                    title: 'Will be used for caption'
                }
                items.push(item)
            }
            return items;
        }
    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
fieldset {
    background-color: #eeeeee;
    --bs-table-bg: #e2e3e5;
    padding-bottom: 4rem;
}

legend {
    background-color:  #ECFDDD;
    color: black;
    padding: 5px 10px;
}
.imagePreviewWrapper {
    width: 250px;
    height: 250px;
    display: block;
    cursor: pointer;
    margin: 0 auto 30px;
    background-size: cover;
    background-position: center center;
}
.pswp__img--placeholder--blank{
    display: none !important;
}
.gallery-thumbnail a img {
    object-fit: contain !important;
    max-width: 6rem!important;
    margin: 0!important;
    margin-bottom: 0!important;
    margin-top: 0!important;
}
.gallery-thumbnail{
    display: flex!important;
    margin: 0 0.2rem 0.09rem 0!important;
}
.pswp__bg {
    background-color: #fff !important;
}
.Pswp_bg image-zoom-background {
    background-color: #fff !important;
}
div.my-gallery{
    display: inline-flex!important;
    max-width: 1rem!important;
}

</style>
