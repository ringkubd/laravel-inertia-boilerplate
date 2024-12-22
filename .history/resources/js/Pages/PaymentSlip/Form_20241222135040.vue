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
                                <option v-for="session in academic_sessions" :key="session.id" :value="session.session">
                                    {{ session.session }}
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
                        <label for="attachment">Attachment (Image or PDF only)<span class="text-danger">*</span></label>
                        <input
                            type="file"
                            id="attachment"
                            class="form-control"
                            ref="attachment"
                            @input="handleFileUpload"
                            accept="image/*,.pdf"
                        >
                        <div v-if="errors.attachment" class="text-danger">
                            {{ errors.attachment }}
                        </div>

                        <!-- File Preview -->
                        <div v-if="preview" class="mt-2">
                            <img v-if="isImage" :src="preview" class="max-w-xs h-auto"/>
                            <div v-else-if="isPDF" class="p-2 border rounded">
                                <span class="text-sm">PDF File: {{ fileName }}</span>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div v-if="form.progress" class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: `${form.progress}%` }"></div>
                            </div>
                            <span class="text-sm">{{ Math.round(form.progress) }}%</span>
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
            preview: null,
            fileName: null,
            isImage: false,
            isPDF: false,
            students: []
        }
    },
    methods:{
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.match('image.*') && file.type !== 'application/pdf') {
                alert('Please upload only images or PDF files');
                event.target.value = '';
                return;
            }

            this.fileName = file.name;
            this.isImage = file.type.match('image.*');
            this.isPDF = file.type === 'application/pdf';

            // Create preview
            if (this.isImage) {
                this.preview = URL.createObjectURL(file);
            } else {
                this.preview = null;
            }

            this.form.attachment = file;
        },
        fetchStudents() {
            axios.get(route('payment-slip.students'), {
                params: { academic_session: this.form.academic_session }
            }).then(response => {
                this.students = response.data;
            }).catch(error => {
                console.error("There was an error fetching the students:", error);
            });
        },
        submitData(){
            this.form.post(this.createForm ? route('payment-slip.store') : route('payment-slip.update', this.paymentSlip.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.preview = null;
                    this.fileName = null;
                }
            });
        }
    },
    watch: {
        'form.academic_session': function(newVal, oldVal) {
            if (newVal) {
                this.fetchStudents();
            }
        }
    },
    beforeUnmount() {
        // Clean up object URL
        if (this.preview) {
            URL.revokeObjectURL(this.preview);
        }
    }
}
</script>

<style scoped>
.max-w-xs {
    max-width: 20rem;
}
</style>
