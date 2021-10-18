<template>
    <div class="w-7/10">
        <div class="card mt-5 min-vh-100">
            <div class="card-header">
                <back style="margin-top: 0!important" class="mt-0" :back-url="route('polytechnic.student.index')"></back>
            </div>
            <div class="card-body">
                <form action="" enctype="multipart/form-data" @submit.prevent="submitForm">
                    <div class="bg-danger">
                        <ul>
                            <li class="alert-danger" v-for="error in form.errors">{{error}}</li>
                        </ul>
                    </div>
                    <fieldset class="row form-group" v-if="createForm">
                        <legend>Student</legend>
                        <div class="row">
                            <label class="col-3" for="student">Select a Student</label>
                            <select required name="student" id="student" class="col-9" v-model="form.student_id">
                                <option value=""></option>
                                <option :value="st.id" v-for="st in students">{{ st.name }}</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Academic Information (Diploma)</legend>
                        <div class="row">
                            <div class="col-md">
                                <label for="institute">Polytechnic</label>
                                <select required name="" id="institute" v-model="form.polytechnic" class="form-control">
                                    <option v-for="poly in polytechnic" :value="poly.id">{{poly.name}}</option>
                                </select>
                                <div v-if="errors.polytechnic" class="text-danger">
                                    {{ errors.polytechnic }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="diploma_session">Session</label>
                                <select required name="diploma_session" id="diploma_session" class="form-control" v-model="form.polytechnic_session">
                                    <option v-for="acsession in academic_session" :value="acsession.session">{{acsession.session}}</option>
                                </select>
                                <div v-if="errors.polytechnic_session" class="text-danger">
                                    {{ errors.polytechnic_session }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="semester">Semester</label>
                                <select required name="" id="semester" v-model="form.semester" class="form-control">
                                    <option v-for="index in 8" :value="index">{{index}}</option>
                                </select>
                                <div v-if="errors.semester" class="text-danger">
                                    {{ errors.semester }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <label for="polytechnic_trade">Polytechnic Trade</label>
                                <select required name="polytechnic_trade" id="polytechnic_trade" class="form-control" v-model="form.polytechnic_trade_id">
                                    <option v-for="trade in trade_polytechnic" :value="trade.name">{{trade.name}}</option>
                                </select>
                                <div v-if="errors.polytechnic_trade" class="text-danger">
                                    {{ errors.polytechnic_trade }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="polytechnic_registration">Polytechnic Registration</label>
                                <input type="number" id="polytechnic_registration" v-model="form.polytechnic_registration" class="form-control">
                                <div v-if="errors.polytechnic_registration" class="text-danger">
                                    {{ errors.polytechnic_registration }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="polytechnic_roll">Polytechnic Roll</label>
                                <input type="number" id="polytechnic_roll" v-model="form.polytechnic_roll" class="form-control">
                                <div v-if="errors.polytechnic_roll" class="text-danger">
                                    {{ errors.polytechnic_roll }}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Bank Information</legend>
                        <div class="row">
                            <div class="col">
                                <label for="bank_name">Name</label>
                                <input type="text" id="bank_name" v-model="form.bank_name" class="form-control" placeholder="Islami Bank">
                                <div v-if="errors.bank_name" class="text-danger">
                                    {{ errors.bank_name }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="account">Account</label>
                                <input type="number" id="account" class="form-control" v-model="form.bank_account" placeholder="1234567890123">
                                <div v-if="errors.bank_account" class="text-danger">
                                    {{ errors.bank_account }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="bank_branch">Branch</label>
                                <input type="text" name="" id="bank_branch" v-model="form.bank_branch" class="form-control" placeholder="Agargaon"/>
                                <div v-if="errors.bank_branch" class="text-danger">
                                    {{ errors.bank_branch }}
                                </div>
                            </div>
                        </div>
                        <input type="hidden" v-model="currentSession">
                        <input type="hidden" v-model="currentTrade">
                        <input type="hidden" v-model="currentClass">
                    </fieldset>
                    <div class="row form-group">
                        <div class="col-4 justify-content-center align-items-end">
                            <input type="submit" class="btn btn-success rounded" v-if="createForm" value="Create">
                            <input type="submit" class="btn btn-warning rounded" v-else value="Update">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'
import InputError from "@/Components/InputError";
import Back from "@/Shared/Back";
export default {
    name: "Form",
    components: {
        Back,
        InputError
    },
    props: [
        'student', 'students', 'user', 'createForm',
        'postForm', 'trade_polytechnic', 'academic_session',
        'banks', 'selected_bank', 'selected_trade', 'selected_session',
        'polytechnic', 'errors', 'classes'
    ],
    data(){
        return {
            form: useForm({
                student_id: this.student.id,
                trade: this.student.trade,
                polytechnic_trade_id: this.student.polytechnic_trade_id,
                polytechnic: this.student.polytechnic,
                polytechnic_registration: this.student.polytechnic_registration,
                polytechnic_roll: this.student.polytechnic_roll,
                polytechnic_session: this.student.polytechnic_session,
                semester: this.student.semester,
                bank_account: this.student.bank_account,
                bank_branch: this.student.bank_branch,
                bank_name: this.selected_bank,
                current_session: this.selected_session,
                classroom: '',
                _method: this.createForm ? 'POST' : 'PUT'
            }),
            photoPreview: this.student.photo,
            idCardPreview: this.student.id_card,
        }
    },
    methods: {
        submitForm(){
            this.postForm(this.form)
        }
    },
    mounted() {
        console.log(this.classes)
    },
    computed: {
        currentSession(){
            this.form.current_session = this.form.polytechnic_session;
            return this.form.current_session;
        },
        currentTrade(){
            this.form.trade = this.form.polytechnic_trade_id;
            return this.form.trade;
        },
        currentClass(){
            let semesterClass = {
                1: "First Semester",
                2: "Second Semester",
                3: "Third Semester",
                4: "Fourth Semester",
                5: "Fifth Semester",
                6: "Sixth Semester",
                7: "Seventh Semester",
                8: "Eighth Semester",
            };
            let __this = this;
            let currentClass = this.classes.filter(function (q){
                let currentSemester = semesterClass[__this.form.semester]
                if (q.name === currentSemester){
                    return q;
                }
            });
            if(currentClass.length > 0){
                this.form.classroom = currentClass[0].id
            }
            return this.form.classroom;
        }
    }
}
</script>

<style scoped>
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

</style>
