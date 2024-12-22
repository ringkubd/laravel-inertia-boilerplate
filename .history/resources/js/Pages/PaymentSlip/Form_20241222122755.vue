<template>
    <div class="flex justify-center h-screen">
        <form action="" enctype="multipart/form-data" @submit.prevent="submitData">
            <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Payment Slip Details</legend>
                <div class="col">
                    <div class="flex sm:grid-cols-1 grid md:grid-cols-2 flex-grow mb-2">
                        <div class="flex-1 group mr-2">
                            <label for="academic_session">Academic Session<span class="text-danger">*</span></label>
                            <select name="academic_session" id="academic_session" v-model="form.academic_session" required class="form-control">
                                <option value="">Select Session</option>
                                <option v-for="session in academic_sessions" :key="session.id" :value="session.id">
                                    {{ session.name }}
                                </option>
                            </select>
                            <div v-if="errors.academic_session" class="text-danger">
                                {{ errors.academic_session }}
                            </div>
                        </div>
                        <div class="flex-1 mr-2">
                            <label for="semester">Semester<span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-control" v-model="form.semester">
                                <option value="">Select Semester</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                                <option value="3">3rd Semester</option>
                                <option value="4">4th Semester</option>
                                <option value="5">5th Semester</option>
                                <option value="6">6th Semester</option>
                                <option value="7">7th Semester</option>
                                <option value="8">8th Semester</option>
                            </select>
                            <div v-if="errors.semester" class="text-danger">
                                {{ errors.semester }}
                            </div>
                        </div>
                    </div>

                    <div class="flex sm:grid-cols-1 grid md:grid-cols-2 flex-grow mb-2">
                        <div class="flex-1 group mr-2">
                            <label for="student_id">Student<span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id" v-model="form.student_id" required class="form-control">
                                <option value="">Select Student</option>
                                <option v-for="student in students" :key="student.id" :value="student.id">
                                    {{ student.name }}
                                </option>
                            </select>
                            <div v-if="errors.student_id" class="text-danger">
                                {{ errors.student_id }}
                            </div>
                        </div>
                        <div class="flex-1 mr-2">
                            <label for="fee_type">Fee Type<span class="text-danger">*</span></label>
                            <select name="fee_type" id="fee_type" class="form-control" v-model="form.fee_type">
                                <option value="">Select Fee Type</option>
                                <option value="admission">Admission Fee</option>
                                <option value="semester">Semester Fee</option>
                                <option value="exam">Exam Fee</option>
                                <option value="other">Other</option>
                            </select>
                            <div v-if="errors.fee_type" class="text-danger">
                                {{ errors.fee_type }}
                            </div>
                        </div>
                    </div>

                    <div class="flex sm:grid-cols-1 grid md:grid-cols-2 flex-grow mb-2">
                        <div class="flex-1 group mr-2">
                            <label for="amount">Amount<span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" v-model="form.amount" required class="form-control">
                            <div v-if="errors.amount" class="text-danger">
                                {{ errors.amount }}
                            </div>
                        </div>
                        <div class="flex-1 mr-2">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <select name="status" id="status" v-model="form.status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <div v-if="errors.status" class="text-danger">
                                {{ errors.status }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md mb-2">
                        <label for="attachment">Attachment<span class="text-danger">*</span></label>
                        <input type="file" id="attachment" class="form-control" ref="attachment" @input="form.attachment = $event.target.files[0]">
                        <div v-if="errors.attachment" class="text-danger">
                            {{ errors.attachment }}
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="row form-group">
                <div class="col-4 justify-content-center align-items-end">
                    <input type="submit" class="btn btn-success rounded" v-if="createForm" value="Submit">
                    <input type="submit" class="btn btn-warning rounded" v-else value="Update">
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import {useForm} from "@inertiajs/vue3";

export default {
    name: "Form",
    props: {
        errors: Object,
        submitForm: Function,
        createForm: Boolean,
        paymentSlip: Object,
        academic_sessions: Array,
        students: Array,
    },
    data() {
        return {
            form: useForm({
                academic_session: this.paymentSlip?.academic_session,
                semester: this.paymentSlip?.semester,
                student_id: this.paymentSlip?.student_id,
                fee_type: this.paymentSlip?.fee_type,
                amount: this.paymentSlip?.amount,
                status: this.paymentSlip?.status || 'pending',
                attachment: null,
            }),
        }
    },
    methods:{
        submitData(){
            this.submitForm(this.form)
        }
    }
}
</script>

<style scoped>

</style>
