<template>
    <div class="w-7/10">
        <div class="card">
            <div class="card-header">
                <back style="margin-top: 0!important" class="mt-0" :back-url="route('student.index')"></back>
            </div>
            <div class="card-body">
                <form action="" enctype="multipart/form-data" @submit.prevent="submitForm">
                    <div class="bg-danger">
                        <ul>
                            <li class="alert-danger" v-for="error in form.errors">{{error}}</li>
                        </ul>
                    </div>
                    <fieldset class="row form-group">
                        <legend>Personal Information</legend>
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" v-model="form.name" required class="form-control">
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="mobile">Mobile</label>
                                    <input type="tel" id="mobile" v-model="form.mobile" required class="form-control">
                                    <div v-if="errors.mobile" class="text-danger">
                                        {{ errors.mobile }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="mobile_1">Alt. Mobile</label>
                                    <input type="tel" id="mobile_1" v-model="form.mobile_1" class="form-control">
                                    <div v-if="errors.mobile_1" class="text-danger">
                                        {{ errors.mobile_1 }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="dob">DoB</label>
                                    <input type="date" id="dob" v-model="form.dob" required class="form-control">
                                    <div v-if="errors.dob" class="text-danger">
                                        {{ errors.dob }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md">
                                    <label for="father_name">Father's Name</label>
                                    <input type="text"  id="father_name" required v-model="form.father_name" class="form-control">
                                    <div v-if="errors.father_name" class="text-danger">
                                        {{ errors.father_name }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="mother_name">Mother's Name</label>
                                    <input type="text"  id="mother_name" required v-model="form.mother_name" class="form-control">
                                    <div v-if="errors.mother_name" class="text-danger">
                                        {{ errors.mother_name }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="guardian_mobile">Guardian Contact Number</label>
                                    <input type="text"  id="guardian_mobile" v-model="form.guardian_mobile" required class="form-control">
                                    <div v-if="errors.guardian_mobile" class="text-danger">
                                        {{ errors.guardian_mobile }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md">
                                    <label for="present_address">Present Address</label>
                                    <input type="text" required id="present_address" v-model="form.present_address" class="form-control">
                                    <div v-if="errors.present_address" class="text-danger">
                                        {{ errors.present_address }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="permanent_address">Permanent Address</label>
                                    <input type="text"  id="permanent_address" v-model="form.permanent_address" class="form-control">
                                    <div v-if="errors.permanent_address" class="text-danger">
                                        {{ errors.permanent_address }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md">
                                    <label for="nid">NID</label>
                                    <input type="nid" id="nid" v-model="form.nid" class="form-control">
                                    <div v-if="errors.nid" class="text-danger">
                                        {{ errors.nid }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <label for="photo">Photo</label>
                                    <input type="file" id="photo" class="form-control" ref="photo" @input="form.photo = $event.target.files[0]" @change="updatePhotoPreview">
                                    <div v-if="errors.photo" class="text-danger">
                                        {{ errors.photo }}
                                    </div>
                                    <div class="imagePreviewWrapper" v-if="photoPreview"
                                         :style="{ 'background-image': `url(${photoPreview})` }"
                                    ></div>
                                </div>
                                <div class="col-md">
                                    <label for="id_card">ID Card</label>
                                    <input type="file" id="id_card" class="form-control" ref="id_card" @input="form.id_card = $event.target.files[0]" @change="updateIDCardPreview">
                                    <div v-if="errors.id_card" class="text-danger">
                                        {{ errors.id_card }}
                                    </div>
                                    <div class="imagePreviewWrapper" v-if="idCardPreview"
                                         :style="{ 'background-image': `url(${idCardPreview})` }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Academic Information (Dakhil)</legend>
                        <div class="row">
                            <div class="col-md">
                                <label for="madrasa_trade_id">Trade</label>
                                <select name="madrasa_trade_id" id="madrasa_trade_id" class="form-control" v-model="form.madrasa_trade_id" required>
                                    <option v-for="trade in trade_madrasa" :value="trade.name">{{trade.name}}</option>
                                </select>
                                <div v-if="errors.madrasa_trade_id" class="text-danger">
                                    {{ errors.madrasa_trade_id }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="class_roll">Class Roll</label>
                                <input type="number" id="class_roll" v-model="form.class_roll" class="form-control" required>
                                <div v-if="errors.class_roll" class="text-danger">
                                    {{ errors.class_roll }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="madrashas_id">Madrasha</label>
                                <select name="madrashas_id" id="madrashas_id" class="form-control" required v-model="form.madrasha_id">
                                    <option v-for="mad in madrasa" :value="mad.id">{{mad.name}}</option>
                                </select>
                                <div v-if="errors.madrashas_id" class="text-danger">
                                    {{ errors.madrashas_id }}
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md">
                                <label for="ssc_roll">SSC Roll</label>
                                <input type="number" id="ssc_roll" v-model="form.ssc_roll" class="form-control">
                                <div v-if="errors.ssc_roll" class="text-danger">
                                    {{ errors.ssc_roll }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="ssc_registration">SSC Registration</label>
                                <input type="number" id="ssc_registration" v-model="form.ssc_registration" class="form-control">
                                <div v-if="errors.ssc_registration" class="text-danger">
                                    {{ errors.ssc_registration }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="session">Session</label>
                                <select name="session" id="session" class="form-control" required v-model="form.ssc_session">
                                    <option v-for="acsession in academic_session" :value="acsession.session">{{acsession.session}}</option>
                                </select>
                                <div v-if="errors.ssc_session" class="text-danger">
                                    {{ errors.ssc_session }}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Academic Information (Diploma)</legend>
                        <div class="row">
                            <div class="col-md">
                                <label for="institute">Polytechnic</label>
                                <select name="" id="institute" v-model="form.polytechnic" class="form-control">
                                    <option v-for="poly in polytechnic" :value="poly.id">{{poly.name}}</option>
                                </select>
                                <div v-if="errors.polytechnic" class="text-danger">
                                    {{ errors.polytechnic }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="diploma_session">Session</label>
                                <select name="diploma_session" id="diploma_session" class="form-control" required v-model="form.polytechnic_session">
                                    <option v-for="acsession in academic_session" :value="acsession.session">{{acsession.session}}</option>
                                </select>
                                <div v-if="errors.polytechnic_session" class="text-danger">
                                    {{ errors.polytechnic_session }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="semester">Semester</label>
                                <select name="" id="semester" v-model="form.semester" class="form-control">
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
                                <select name="polytechnic_trade" id="polytechnic_trade" class="form-control" v-model="form.polytechnic_trade_id">
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
                    </fieldset>
                    <fieldset class="row form-group">
                        <legend>Login Information</legend>
                        <div class="row">
                            <div class="col">
                                <label for="email">Email</label>
                                <input type="email" id="email" v-model="form.email" class="form-control">
                                <div v-if="errors.email" class="text-danger">
                                    {{ errors.email }}
                                </div>
                            </div>
                            <div class="col">
                                <label for="password">Password</label>
                                <input type="password" id="password" v-model="form.password" class="form-control">
                                <div v-if="errors.password" class="text-danger">
                                    {{ errors.password }}
                                </div>
                            </div>
                        </div>
<!--                        Hidden Element-->
                        <input type="hidden" v-model="currentSession">
                        <input type="hidden" v-model="currentTrade">
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
    props: ['student', 'user', 'createForm', 'postForm', 'trade_polytechnic', 'trade_madrasa', 'academic_session', 'banks', 'selected_bank', 'selected_trade', 'selected_session', 'polytechnic', 'madrasa', 'errors'],
    data(){
        return {
            form: useForm({
                name: this.student.name,
                dob: this.student.dob,
                mobile: this.student.mobile,
                mobile_1: this.student.mobile_1,
                nid: this.student.nid,
                email: this.user.email,
                father_name: this.student.father_name,
                mother_name: this.student.mother_name,
                guardian_mobile: this.student.guardian_mobile,
                present_address: this.student.present_address,
                permanent_address: this.student.permanent_address,
                password: null,
                trade: this.student.trade,
                madrasa_trade_id: this.student.madrasa_trade_id,
                polytechnic_trade_id: this.student.polytechnic_trade_id,
                class_roll: this.student.class_roll,
                madrasha_id: this.student.madrasha_id,
                ssc_roll: this.student.ssc_roll,
                ssc_registration: this.student.ssc_registration,
                ssc_session: this.student.ssc_session,
                polytechnic: this.student.polytechnic,
                polytechnic_registration: this.student.polytechnic_registration,
                polytechnic_roll: this.student.polytechnic_roll,
                polytechnic_session: this.student.polytechnic_session,
                semester: this.student.semester,
                bank_account: this.student.bank_account,
                bank_branch: this.student.bank_branch,
                bank_name: this.selected_bank,
                photo: this.student.photo,
                id_card: this.student.id_card,
                current_session: this.selected_session,
                _method: this.createForm ? 'POST' : 'PUT'
            }),
            photoPreview: this.student.photo,
            idCardPreview: this.student.id_card,
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
            this.$emit('input', this.$refs.photo.files[0])
        },
        submitForm(){
            this.postForm(this.form)
        }
    },
    mounted() {
        if (this.student.photo){
            this.photoPreview = "/"+this.student.photo
        }
        if (this.student.id_card){
            this.idCardPreview = "/"+this.student.photo
        }
    },
    computed: {
        currentSession(){
            if (this.form.polytechnic_session){
                this.form.current_session = this.form.polytechnic_session;
            }else{
                this.form.current_session = this.form.ssc_session;
            }
            return this.form.current_session;
        },
        currentTrade(){
            if (this.form.polytechnic_trade_id){
                this.form.trade = this.form.polytechnic_trade_id;
            }else{
                this.form.trade = this.form.madrasa_trade_id;
            }
            return this.form.trade;
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
    background-color:  #ABA989;
    color: white;
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
