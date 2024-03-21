<template>
    <div>
        <div class="card mt-1 min-h-screen">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('placement.index')"></back>
                    </div>
                    <div class="card-body flex justify-center w-full">
                        <form @submit.prevent="submit" class="w-full sm:max-w-7xl flex flex-col space-y-2">
                            <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-0">
                                <div class="w-full shadow-lg p-3 rounded-2xl">
                                    <label for="academic_sessions">Session</label>
                                    <Select2
                                        :options="academic_sessions"
                                        v-model="formData.academic_session"
                                        select-class="w-full"
                                        id="academic_sessions"
                                        @select="getStudentList"
                                    />
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="w-full shadow-lg p-3 rounded-2xl">
                                    <label for="student_id">Student</label>
                                    <Select2
                                        :options="student_list"
                                        v-model="formData.student_id"
                                        class="w-full py-1.5"
                                        id="student_list"
                                    />
                                    <div v-if="errors.student_id" class="text-danger">
                                        {{ errors.student_id }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                <div class="w-full shadow-lg p-3 rounded-2xl">
                                    <label for="final_result">Final Result (CGPA)</label>
                                    <input type="text" name="final_result" id="final_result" v-model="formData.final_result" class="form-control">
                                    <div v-if="errors.final_result" class="text-danger">
                                        {{ errors.final_result }}
                                    </div>
                                </div>
                                <div class="w-full shadow-lg p-3 rounded-2xl">
                                    <label for="present_status">Present Status</label>
                                    <Select2
                                        v-model="formData.present_status"
                                        id="present_status"
                                        :options="[
                                            {id: 'higher_study', text: 'Higher Study'},
                                            {id: 'employment', text: 'Job'},
                                            {id: 'other', text: 'Other'},
                                            ]"
                                    />
                                    <div v-if="errors.present_status" class="text-danger">
                                        {{ errors.present_status }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="formData.present_status === 'employment'" class="flex flex-col space-y-0 sm:space-y-4">
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="organization">Organization</label>
                                        <input type="text" name="organization" id="organization" v-model="formData.employment.organization" class="form-control">
                                        <div v-if="errors['employment.organization']" class="text-danger">
                                            {{ errors['employment.organization'] }}
                                        </div>
                                    </div>
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="salary">Salary</label>
                                        <input type="number" name="salary" id="salary" v-model="formData.employment.salary" class="form-control">
                                        <div v-if="errors['employment.salary']" class="text-danger">
                                            {{ errors['employment.salary'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="location">Address</label>
                                        <input type="text" name="location" id="location" v-model="formData.employment.location" class="form-control">
                                    </div>
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="position">Position</label>
                                        <input type="text" name="position" id="position" v-model="formData.employment.position"  class="form-control">
                                        <div v-if="errors['employment.position']" class="text-danger">
                                            {{ errors['employment.position'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" v-model="formData.employment.start_date" class="form-control">
                                    </div>
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" v-model="formData.employment.end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div v-if="formData.present_status === 'other'" class="flex flex-col space-y-0 sm:space-y-4">
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="other">Other</label>
                                        <textarea name="note" id="note" v-model="formData.other.note" class="form-control" />
                                        <div v-if="errors['other.note']" class="text-danger">
                                            {{ errors['other.note'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="formData.present_status === 'higher_study'" class="flex flex-col space-y-0 sm:space-y-4">
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="institute_name">Institute Name</label>
                                        <input type="text" name="institute_name" id="institute_name" v-model="formData.higher_study.institute_name" class="form-control">
                                        <div v-if="errors['higher_study.institute_name']" class="text-danger">
                                            {{ errors['higher_study.institute_name'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 space-y-2 sm:space-y-0">
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="degree">Degree name</label>
                                        <input type="text" name="degree" id="degree" v-model="formData.higher_study.degree" class="form-control">
                                        <div v-if="errors['higher_study.degree']" class="text-danger">
                                            {{ errors['higher_study.degree'] }}
                                        </div>
                                    </div>
                                    <div class="w-full shadow-lg p-3 rounded-2xl">
                                        <label for="semester">Semester</label>
                                        <input type="text" name="semester" id="semester" v-model="formData.higher_study.semester" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="justify-content-center items-center justify-center">
                                <input type="submit" value="Submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Back from "@/Shared/Back.vue";
import axios from "axios";
import Select2 from "@/Components/Select2.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/inertia-vue3";
export default {
    name: "add",
    components: {Input, Select2, Back},
    props: ['placement', 'flash', 'academic_sessions', 'submitForm', 'errors'],
    data(){
        return {
            formData: useForm({
                academic_session: '',
                student_id: '',
                present_status: 'employment',
                final_result: '',
                employment: {
                    organization: '',
                    salary: '',
                    location: '',
                    position: '',
                    start_date: '',
                    end_date: '',
                },
                other: {
                    note: ''
                },
                higher_study: {
                    institute_name: '',
                    degree: '',
                    semester: '',
                }
            }),
            student_list: [],
        };
    },
    methods: {
        submit(){
            this.submitForm(this.formData)
        },
        getStudentList(){
            const ax = axios.get(route('student_list'), {
                params: this.formData
            }).then(({data}) => {
                this.student_list = data.map((student) => {
                    return {
                        id: student.value,
                        text: student.label,
                        result: student.polytechnic_result
                    }
                });
            })
        }
    },
    mounted() {

    },
    updated() {
        console.log(this.errors)

    },
    computed(){
        this.student_list;
    }
}
</script>

<style scoped>

</style>
