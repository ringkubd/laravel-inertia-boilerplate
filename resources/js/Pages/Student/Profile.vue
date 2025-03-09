<template>
    <Head>
        <title>Student Profile | IsDB-BISEW</title>
    </Head>
    <Authenticated>
        <div class="container mt-4 d-flex flex-column align-items-center">
            <div class="text-center mb-4 w-100">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <Back :back-url="$page.props.urlPrev" />
                    <div class="text-center d-flex flex-column align-items-center">
                        <img class="rounded-circle" :src="photoExists(student.photo) ? '/' + student.photo : 'https://picsum.photos/200/300'" :alt="student.name" style="width: 150px; height: 150px; object-fit: cover;">
                        <h1 class="mt-3">{{ student.name }}</h1>
                    </div>
                    <div></div> <!-- Empty div to balance the layout -->
                </div>
            </div>
            <div class="row w-100">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h2>Personal Information</h2>
                            <button @click="printSection('personal-info')" class="btn btn-light">Print</button>
                        </div>
                        <div class="card-body" id="personal-info">
                            <table class="table">
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
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h2>Academic Information (Dakhil)</h2>
                            <button @click="printSection('academic-info-dakhil')" class="btn btn-light">Print</button>
                        </div>
                        <div class="card-body" id="academic-info-dakhil">
                            <table class="table">
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
                <div class="col-md-12" v-if="student.madrasa_completed === 1 && student.polytechnic !== null">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-white text-center d-flex justify-content-between align-items-center">
                            <h2>Academic Information (Diploma)</h2>
                            <button @click="printSection('academic-info-diploma')" class="btn btn-light">Print</button>
                        </div>
                        <div class="card-body" id="academic-info-diploma">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>Trade</th>
                                            <td>{{ student.polytechnic_trade_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Session</th>
                                            <td>{{ student.polytechnic_session }}</td>
                                        </tr>
                                        <tr>
                                            <th>Semester Continuing</th>
                                            <td>
                                                <span v-if="student.polytechnic_result?.some(result => result.status === 'Dropout')">
                                                    Dropped out in Semester {{ student.polytechnic_result.find(result => result.status === 'Dropout').semester }}
                                                </span>
                                                <span v-else>
                                                    {{ student.polytechnic_completed == 1 ? "Passed" : student.polytechnic_result?.reduce((max, result) => Math.max(max, result.semester), 0) + 1 }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Registration</th>
                                            <td>{{ student.polytechnic_registration }}</td>
                                        </tr>
                                        <tr>
                                            <th>Roll</th>
                                            <td>{{ student.polytechnic_roll }}</td>
                                        </tr>
                                        <tr>
                                            <th>Institute Name</th>
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
                                <div class="col-md-6">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Semester</th>
                                            <th>GPA</th>
                                            <th>Status</th>
                                            <th>Failed Subjects</th>
                                            <th>Attachments</th>
                                            <th>Added By</th>
                                            <th>Approved By</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="result in student.polytechnic_result" :key="result.id">
                                            <td>{{ result.semester }}</td>
                                            <td>{{ result.gpa }}</td>
                                            <td>{{ result.status }}</td>
                                            <td>{{ result.failed_in_subject }}</td>
                                            <td>
                                                <img v-if="result?.attachments?.length > 0" v-for="attachment in result?.attachments" :src="'/' + attachment.attachment" alt="" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
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
                <div class="col-md-12" v-if="student.invoice_details?.length > 0">
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger text-white text-center d-flex justify-content-between align-items-center">
                            <h2>Invoice Details</h2>
                            <button @click="printSection('invoice-details')" class="btn btn-light">Print</button>
                        </div>
                        <div class="card-body" id="invoice-details">
                            <!-- Payment Status Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h5>Total Payment Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Paid:</strong> {{ totalPaidAmount }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h5>Payment by Fee Type</h5>
                                        </div>
                                        <div class="card-body">
                                            <div v-for="(amount, feeType) in paymentsByFeeType" :key="feeType">
                                                <strong>{{ feeType }}:</strong> {{ amount }} BDT
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h5>Payment by Semester</h5>
                                        </div>
                                        <div class="card-body">
                                            <div v-for="(amount, semester) in paymentsBySemester" :key="semester">
                                                <strong>Semester {{ semester }}:</strong> {{ amount }} BDT
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Details Table -->
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Invoice Month</th>
                                    <th>Semester</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Invoice Raising Date</th>
                                    <th>Payment Method</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="invoice in student.invoice_details" :key="invoice.id">
                                    <td>{{ invoice.invoice.invoice_id }}</td>
                                    <td>{{ invoice.invoice_month }}</td>
                                    <td>{{ invoice.invoice.semester }}</td>
                                    <td>{{ invoice.fee_type }}</td>
                                    <td>{{ invoice.amount }}</td>
                                    <td>{{ invoice.invoice.invoice_date }}</td>
                                    <td>{{ invoice.invoice.bank_name }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Back from "@/Shared/Back";
export default {
    name: "Profile",
    props: ['student'],
    components: {Back, Authenticated},
    computed: {
        totalPaidAmount() {
            if (!this.student.invoice_details || this.student.invoice_details.length === 0) {
                return 0;
            }
            return this.student.invoice_details.reduce((total, invoice) => {
                return total + (parseFloat(invoice.amount) || 0);
            }, 0);
        },
        paymentsByFeeType() {
            if (!this.student.invoice_details || this.student.invoice_details.length === 0) {
                return {};
            }

            return this.student.invoice_details.reduce((acc, invoice) => {
                const feeType = invoice.fee_type || 'Unknown';
                if (!acc[feeType]) {
                    acc[feeType] = 0;
                }
                acc[feeType] += (parseFloat(invoice.amount) || 0);
                return acc;
            }, {});
        },
        paymentsBySemester() {
            if (!this.student.invoice_details || this.student.invoice_details.length === 0) {
                return {};
            }

            return this.student.invoice_details.reduce((acc, invoice) => {
                const semester = invoice.invoice.semester || 'Unknown';
                if (!acc[semester]) {
                    acc[semester] = 0;
                }
                acc[semester] += (parseFloat(invoice.amount) || 0);
                return acc;
            }, {});
        }
    },
    methods: {
        photoExists(photo) {
            const http = new XMLHttpRequest();
            http.open('HEAD', '/' + photo, false);
            http.send();
            return http.status !== 404;
        },
        printSection(sectionId) {
            // Get section title from the corresponding card header
            const cardHeader = document.getElementById(sectionId).closest('.card').querySelector('.card-header h2').innerText;

            // Get the section content
            const sectionContent = document.getElementById(sectionId).innerHTML;

            // Create print container with header
            const printContent = `
                <div class="print-header" style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #333;">
                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <img style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 15px;"
                            src="${this.photoExists(this.student.photo) ? '/' + this.student.photo : 'https://picsum.photos/200/300'}"
                            alt="${this.student.name}">
                        <div>
                            <h1 style="margin: 0; font-size: 24px;">${this.student.name}</h1>
                            <p style="margin: 5px 0 0 0; font-size: 14px;">IsDB-BISEW Student Profile</p>
                        </div>
                    </div>
                    <h2 style="margin: 10px 0 0 0; font-size: 20px; color: #333;">${cardHeader}</h2>
                </div>
                <div class="print-content">
                    ${sectionContent}
                </div>
                <div class="print-footer" style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                </div>
            `;

            // Store original content
            const originalContents = document.body.innerHTML;

            // Replace content with our print content
            document.body.innerHTML = printContent;

            // Print
            window.print();

            // Restore original content
            document.body.innerHTML = originalContents;
        }
    },
    mounted() {
        console.log(this.student)
    }
}
</script>

<style scoped>
.card {
    border-radius: 10px;
}
.card-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
.table th, .table td {
    vertical-align: middle;
}
@media print {
    .print-header {
        display: block !important;
    }
    .print-content table {
        width: 100%;
        border-collapse: collapse;
    }
    .print-content th, .print-content td {
        border: 1px solid #ddd;
        padding: 8px;
    }
}
</style>
