<template>
    <Head>
        <title>Add Teacher|Four year diploma</title>
    </Head>
    <div class="w-7/10">
        <div class="card mt-1">
            <div class="card-header">
                <back style="margin-top: 0!important" class="mt-0" :back-url="route('teacher.index')"></back>
            </div>
            <div class="card-body">
                <form action="" enctype="multipart/form-data" @submit.prevent="submitForm">
                    <div class="bg-danger">
                        <ul>
                            <li class="alert-danger" v-for="error in form.errors">{{error}}</li>
                        </ul>
                    </div>
                    <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                        <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Personal Information</legend>
                        <div class="col">
                            <div class="flex sm:grid-cols-1 grid md:grid-cols-3 flex-grow mb-2">
                                <div class="flex-1 group mr-2">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" v-model="form.name" required class="form-control">
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="flex-1 mr-2">
                                    <label for="mobile">Mobile</label>
                                    <input type="tel" id="mobile" v-model="form.mobile" required class="form-control">
                                    <div v-if="errors.mobile" class="text-danger">
                                        {{ errors.mobile }}
                                    </div>
                                </div>
                                <div class="flex-1 mr-2">
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
                                    <div class="imagePreviewWrapper" v-if="photoPreview" :style="{ 'background-image': `url(${photoPreview})` }"></div>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                    <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                        <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Job Information</legend>
                        <div class="row">
                            <div class="col-md">
                                <label for="trade_id">Trade</label>
                                <select name="madrasa_trade_id" id="madrasa_trade_id" class="form-control" v-model="form.trade_id" required>
                                    <option v-for="trade in trades" :value="trade.id">{{trade.name}}</option>
                                </select>
                                <div v-if="errors.trade_id" class="text-danger">
                                    {{ errors.trade_id }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="madrashas_id">Madrasha</label>
                                <select name="madrashas_id" id="madrashas_id" class="form-control" required v-model="form.madrashas_id">
                                    <option v-for="mad in madrasa" :value="mad.id">{{mad.name}}</option>
                                </select>
                                <div v-if="errors.madrashas_id" class="text-danger">
                                    {{ errors.madrashas_id }}
                                </div>
                            </div>
                            <div class="col-md">
                                <label for="joining_date">Joining Date</label>
                                <input type="date" class="form-control" id="joining_date" v-model="form.joining_date">
                                <div v-if="errors.joining_date" class="text-danger">
                                    {{ errors.joining_date }}
                                </div>
                            </div>

                            <div class="col-md">
                                <label for="designation">Designation</label>
                                <select name="designation" id="designation" class="form-control" required v-model="form.designation">
                                    <option v-for="des in designation" :value="des.name">{{des.name}}</option>
                                </select>
                                <div v-if="errors.designation" class="text-danger">
                                    {{ errors.designation }}
                                </div>
                            </div>

                        </div>
                    </fieldset>
                    <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                        <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Bank Information</legend>
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
                    <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                        <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Login Information</legend>
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
import {useForm} from '@inertiajs/vue3'
import InputError from "@/Components/InputError";
import Back from "@/Shared/Back";
export default {
    name: "Form",
    components: {
        Back,
        InputError
    },
    props: ['teacher', 'user', 'createForm', 'postForm', 'trades', 'banks', 'selected_bank', 'selected_trade', 'madrasa', 'errors', 'designation'],
    data(){
        return {
            form: useForm({
                name: this.teacher.name,
                dob: this.teacher.dob,
                mobile: this.teacher.mobile,
                nid: this.teacher.nid,
                email: this.user.email,
                father_name: this.teacher.father_name,
                mother_name: this.teacher.mother_name,
                present_address: this.teacher.present_address,
                permanent_address: this.teacher.permanent_address,
                password: null,
                trade_id: this.teacher.trade_id,
                madrashas_id: this.teacher.madrashas_id,
                bank_account: this.teacher.bank_account,
                bank_branch: this.teacher.bank_branch,
                bank_name: this.teacher.bank_name,
                photo: this.teacher.photo,
                designation: this.teacher.designation,
                joining_date: this.teacher.joining_date,
                _method: this.createForm ? 'POST' : 'PUT'
            }),
            photoPreview: this.teacher.photo,
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
        submitForm(){
            this.postForm(this.form)
        }
    },
    mounted() {
        if (this.teacher.photo){
            this.photoPreview = "/"+this.teacher.photo
        }
    },
    computed: {
    }
}
</script>

<style scoped>
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
