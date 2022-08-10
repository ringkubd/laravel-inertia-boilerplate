<template>
    <div class="w-7/10">
        <div class="card mt-1 min-vh-100">
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
                                <option :value="st.id" v-for="st in students">{{ st.name +"-"+ st.ssc_session }}</option>
                            </select>
                            <div class="flex flex-row mt-4 justify-between border-b-2">
                                <div class="mx-2"><strong>Name: </strong> {{studentId?.name}}</div>
                                <div class="mx-2"><strong>Father: </strong> {{studentId?.father_name}}</div>
                                <div class="mx-2"><strong>Madrasah: </strong> {{studentId?.madrasha?.name}}</div>
                                <div class="mx-2"><strong>Trade: </strong> {{studentId?.trade}}</div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Academic Information (Diploma)</legend>
                        <div class="row">
                            <div class="col-md">
                                <label for="institute">Polytechnic</label>
                                <select required name="" id="institute" v-model="form.polytechnic_id" class="form-control">
                                    <option v-for="poly in polytechnic" :value="poly.id">{{poly.name}}</option>
                                </select>
                                <div v-if="errors.polytechnic_id" class="text-danger">
                                    {{ errors.polytechnic_id }}
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
                                    <option v-for="index in 1" :value="index">{{index}}</option>
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
                    <fieldset class="row form-group" v-if="super_admin">
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
                                <input type="text" id="account" class="form-control" v-model="form.bank_account" placeholder="1234567890123">
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
                            <div class="col">
                                <label for="bank_document">Bank Document</label>
                                <input type="file" id="bank_document" class="form-control" ref="bank_document" @input="form.bank_document = $event.target.files[0]" @change="updateBankDocumentPreview" accept="image/png, image/jpeg">
                                <div v-if="errors.bank_document" class="text-danger">
                                    {{ errors.bank_document }}
                                </div>
                                <div class="imagePreviewWrapper" v-if="bankDocumentPreview && bankDocumentPreview !== ''" :style="{ 'background-image': `url(${bankDocumentPreview})` }"></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="row form-group">
                        <input type="hidden" v-model="currentSession">
                        <input type="hidden" v-model="currentTrade">
                        <input type="hidden" v-model="currentClass">
                        <legend>Photo & ID</legend>
                        <div class="col-md">
                            <label for="photo">Photo</label>
                            <input type="file" id="photo" class="form-control" ref="photo" @input="form.photo = $event.target.files[0]" @change="updatePhotoPreview" accept="image/png, image/jpeg">
                            <div v-if="errors.photo" class="text-danger">
                                {{ errors.photo }}
                            </div>
                            <div class="imagePreviewWrapper" v-if="photoPreview && photoPreview !== ''" :style="{ 'background-image': `url(${photoPreview})` }"></div>
                        </div>
                        <div class="col-md">
                            <label for="id_card">ID Card</label>
                            <input type="file" id="id_card" class="form-control" ref="id_card" @input="form.id_card = $event.target.files[0]" @change="updateIDCardPreview" accept="image/png, image/jpeg">
                            <div v-if="errors.id_card" class="text-danger">
                                {{ errors.id_card }}
                            </div>
                            <div class="imagePreviewWrapper" v-if="idCardPreview && idCardPreview !== ''"
                                 :style="{ 'background-image': `url(${idCardPreview})` }"
                            ></div>
                        </div>
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
        'polytechnic', 'errors', 'classes', 'super_admin'
    ],
    data(){
        return {
            form: useForm({
                student_id: this.student.id,
                trade: this.student.trade,
                polytechnic_trade_id: this.student.polytechnic_trade_id,
                polytechnic_id: this.student.polytechnic_id,
                polytechnic_registration: this.student.polytechnic_registration,
                polytechnic_roll: this.student.polytechnic_roll,
                polytechnic_session: this.student.polytechnic_session,
                semester: this.student.semester,
                bank_account: this.student.bank_account,
                bank_branch: this.student.bank_branch,
                bank_name: this.selected_bank,
                bank_document: this.student.bank_document,
                current_session: this.selected_session,
                classroom: '',
                photo: this.student.photo,
                id_card: this.student.id_card,
                _method: this.createForm ? 'POST' : 'PUT'
            }),
            photoPreview: this.student.photo,
            idCardPreview: this.student.id_card,
            bankDocumentPreview: this.student.bank_document,
            selected_student: {}
        }
    },
    methods: {
        updatePhotoPreview() {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };
            reader.readAsDataURL(this.$refs.photo.files[0]);
            this.$emit('input', this.$refs.photo.files[0])
        },
        updateIDCardPreview() {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.idCardPreview = e.target.result;
            };
            reader.readAsDataURL(this.$refs.id_card.files[0]);
            this.$emit('input', this.$refs.id_card.files[0])
        },
        updateBankDocumentPreview() {
            const reader = new FileReader();
            console.log(reader)
            reader.onload = (e) => {
                this.bankDocumentPreview = e.target.result;
            };
            reader.readAsDataURL(this.$refs.bank_document.files[0]);
            this.$emit('input', this.$refs.bank_document.files[0])
        },
        submitForm(){
            this.postForm(this.form)
        },
        selectStudent(e){
            const studentId = e.target.value
            this.selected_student = this.students.filter((student) => {
                return e.target.value === student.id
            })
            console.log(this.selected_student)
        }
    },
    mounted() {
        if (this.student.photo){
            this.photoPreview = "/"+this.student.photo
        }
        if (this.student.id_card){
            this.idCardPreview = "/"+this.student.id_card
        }
        if (this.student.bank_document){
            this.bankDocumentPreview = "/"+this.student.bank_document
        }
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
        },
        studentId(){
            return this.selected_student = this.students.filter((student) => {
                return this.form.student_id === student.id
            })[0]
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
