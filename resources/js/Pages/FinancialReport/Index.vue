<template>
    <Head>
        <title>Financial Report</title>
    </Head>
    <Authenticated>
        <template #header>
            <div class="flex items-center justify-between">
                <PageHeader>Financial Report</PageHeader>
                <button @click="print" class="btn btn-success text-sm px-3 py-1">Print</button>
            </div>
        </template>
        <div class="container-fluid py-3">
            <div class="card" id="printArea">
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6 no-print">
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-brand-600">{{ Number(summary.total_paid).toLocaleString() }}</div>
                            <div class="text-sm text-gray-500 mt-1">Total Paid</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-accent-600">{{ summary.total_students_paid }}</div>
                            <div class="text-sm text-gray-500 mt-1">Students Paid</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ Number(summary.avg_per_student_overall).toLocaleString() }}</div>
                            <div class="text-sm text-gray-500 mt-1">Avg/Student</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ summary.total_completed }}</div>
                            <div class="text-sm text-gray-500 mt-1">Completed</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ Number(summary.completed_total_received).toLocaleString() }}</div>
                            <div class="text-sm text-gray-500 mt-1">Completed Total</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-6 p-4 bg-gray-50 rounded-lg no-print">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                            <select v-model="filterSession" class="form-control" @change="applyFilter">
                                <option value="">All Sessions</option>
                                <option v-for="s in sessions" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <select v-model="filterSemester" class="form-control" @change="applyFilter">
                                <option value="">All Semesters</option>
                                <option v-for="n in 8" :value="n">{{ n }}{{ ordinal_suffix_of(n) }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" v-model="filterFromDate" class="form-control" @change="applyFilter">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" v-model="filterToDate" class="form-control" @change="applyFilter">
                        </div>
                        <div class="flex items-end gap-1">
                            <button @click="applyFilter" class="btn btn-success">Apply</button>
                            <button @click="resetFilter" class="btn btn-warning">Reset</button>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 mb-4 no-print">
                        <nav class="flex flex-wrap -mb-px gap-1">
                            <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
                                :class="activeTab === tab.key
                                    ? 'inline-block px-4 py-2 border-b-2 border-brand-600 text-brand-700 font-medium text-sm'
                                    : 'inline-block px-4 py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 text-sm'"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <div v-if="activeTab === 'monthly'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Total Amount</th>
                                    <th class="text-center">Total Students</th>
                                    <th class="text-center">Students Paid</th>
                                    <th class="text-center">Not Paid</th>
                                    <th class="text-right">Avg/Student</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in monthly" :key="i">
                                    <td>{{ row.month }}</td>
                                    <td class="text-right">{{ Number(row.total_amount).toLocaleString() }}</td>
                                    <td class="text-center">{{ row.total_students }}</td>
                                    <td class="text-center text-accent-600 font-medium">{{ row.students_paid }}</td>
                                    <td class="text-center text-red-600 font-medium">{{ row.students_not_paid }}</td>
                                    <td class="text-right">{{ Number(row.avg_per_student).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'completed'">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-indigo-700">{{ completedSummary.total_students }}</div>
                                <div class="text-xs text-gray-500">Completed Students</div>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-indigo-700">{{ Number(completedSummary.avg_total_per_student).toLocaleString() }}</div>
                                <div class="text-xs text-gray-500">Avg Total/Student</div>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                <div class="text-xl font-bold text-indigo-700">{{ Number(completedSummary.avg_monthly_all).toLocaleString() }}</div>
                                <div class="text-xs text-gray-500">Avg Monthly/Student</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-secondary">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Session</th>
                                        <th class="text-right">Total Received</th>
                                        <th class="text-center">Months Active</th>
                                        <th class="text-right">Avg Monthly</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(s, i) in completedByStudent" :key="i">
                                        <td>{{ s.name }}</td>
                                        <td>{{ s.polytechnic_session }}</td>
                                        <td class="text-right">{{ Number(s.total_received).toLocaleString() }}</td>
                                        <td class="text-center">{{ s.months_active }}</td>
                                        <td class="text-right">{{ Number(s.avg_monthly).toLocaleString() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="activeTab === 'session'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th class="text-right">Total Amount</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-right">Avg/Student</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in bySession" :key="i">
                                    <td>{{ row.session }}</td>
                                    <td class="text-right">{{ Number(row.total_amount).toLocaleString() }}</td>
                                    <td class="text-center">{{ row.students }}</td>
                                    <td class="text-right">{{ Number(row.avg_per_student).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'semester'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Semester</th>
                                    <th class="text-right">Total Amount</th>
                                    <th class="text-center">Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in bySessionSemester" :key="i">
                                    <td>{{ row.session }}</td>
                                    <td>{{ ordinal_suffix_of(row.semester) }}</td>
                                    <td class="text-right">{{ Number(row.total_amount).toLocaleString() }}</td>
                                    <td class="text-center">{{ row.students }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="print-template" style="display:none;">
            <div style="padding:20px;font-family:sans-serif;">
                <h2 style="text-align:center;margin-bottom:20px;">Financial Report</h2>
                <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:120px;border:1px solid #ccc;border-radius:6px;padding:10px;text-align:center;">
                        <div style="font-size:20px;font-weight:bold;color:#0ea5e9;">{{ Number(summary.total_paid).toLocaleString() }}</div>
                        <div style="font-size:11px;color:#666;">Total Paid</div>
                    </div>
                    <div style="flex:1;min-width:120px;border:1px solid #ccc;border-radius:6px;padding:10px;text-align:center;">
                        <div style="font-size:20px;font-weight:bold;color:#22c55e;">{{ summary.total_students_paid }}</div>
                        <div style="font-size:11px;color:#666;">Students Paid</div>
                    </div>
                    <div style="flex:1;min-width:120px;border:1px solid #ccc;border-radius:6px;padding:10px;text-align:center;">
                        <div style="font-size:20px;font-weight:bold;color:#3b82f6;">{{ Number(summary.avg_per_student_overall).toLocaleString() }}</div>
                        <div style="font-size:11px;color:#666;">Avg/Student</div>
                    </div>
                    <div style="flex:1;min-width:120px;border:1px solid #ccc;border-radius:6px;padding:10px;text-align:center;">
                        <div style="font-size:20px;font-weight:bold;color:#6366f1;">{{ summary.total_completed }}</div>
                        <div style="font-size:11px;color:#666;">Completed</div>
                    </div>
                    <div style="flex:1;min-width:120px;border:1px solid #ccc;border-radius:6px;padding:10px;text-align:center;">
                        <div style="font-size:20px;font-weight:bold;color:#6366f1;">{{ Number(summary.completed_total_received).toLocaleString() }}</div>
                        <div style="font-size:11px;color:#666;">Completed Total</div>
                    </div>
                </div>
                <div v-if="activeTab === 'monthly'">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#0c4a6e;color:white;">
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Month</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Total Amount</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Total Students</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Students Paid</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Not Paid</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Avg/Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in monthly" :key="i" style="border:1px solid #ccc;">
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ row.month }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(row.total_amount).toLocaleString() }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ row.total_students }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ row.students_paid }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ row.students_not_paid }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(row.avg_per_student).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="activeTab === 'session'">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#0c4a6e;color:white;">
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Session</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Total Amount</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Students</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Avg/Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in bySession" :key="i" style="border:1px solid #ccc;">
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ row.session }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(row.total_amount).toLocaleString() }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ row.students }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(row.avg_per_student).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="activeTab === 'semester'">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#0c4a6e;color:white;">
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Session</th>
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Semester</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Total Amount</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in bySessionSemester" :key="i" style="border:1px solid #ccc;">
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ row.session }}</td>
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ ordinal_suffix_of(row.semester) }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(row.total_amount).toLocaleString() }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ row.students }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="activeTab === 'completed'">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#0c4a6e;color:white;">
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Student</th>
                                <th style="padding:8px;text-align:left;border:1px solid #ccc;">Session</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Total Received</th>
                                <th style="padding:8px;text-align:center;border:1px solid #ccc;">Months Active</th>
                                <th style="padding:8px;text-align:right;border:1px solid #ccc;">Avg Monthly</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s, i) in completedByStudent" :key="i" style="border:1px solid #ccc;">
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ s.name }}</td>
                                <td style="padding:6px 8px;border:1px solid #ccc;">{{ s.polytechnic_session }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(s.total_received).toLocaleString() }}</td>
                                <td style="padding:6px 8px;text-align:center;border:1px solid #ccc;">{{ s.months_active }}</td>
                                <td style="padding:6px 8px;text-align:right;border:1px solid #ccc;">{{ Number(s.avg_monthly).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";

export default {
    components: { Authenticated, PageHeader },
    props: {
        summary: Object,
        monthly: Array,
        completedByStudent: Array,
        completedSummary: Object,
        bySession: Array,
        bySessionSemester: Array,
        filters: Object,
        sessions: Array,
    },
    data() {
        return {
            filterSession: this.filters?.session || '',
            filterSemester: this.filters?.semester || '',
            filterFromDate: this.filters?.from_date || '',
            filterToDate: this.filters?.to_date || '',
            activeTab: 'monthly',
            tabs: [
                { key: 'monthly', label: 'Monthly' },
                { key: 'session', label: 'Session-wise' },
                { key: 'semester', label: 'Semester-wise' },
                { key: 'completed', label: 'Completed Students' },
            ],
        };
    },
    methods: {
        applyFilter() {
            const params = {};
            if (this.filterSession) params.session = this.filterSession;
            if (this.filterSemester) params.semester = this.filterSemester;
            if (this.filterFromDate) params.from_date = this.filterFromDate;
            if (this.filterToDate) params.to_date = this.filterToDate;
            this.$inertia.get(route('financial.report'), params, { preserveState: true });
        },
        resetFilter() {
            this.filterSession = '';
            this.filterSemester = '';
            this.filterFromDate = '';
            this.filterToDate = '';
            this.$inertia.get(route('financial.report'), {}, { preserveState: true });
        },
        print() {
            this.$htmlToPaper('print-template', {
                name: 'financial-report',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
                    '/css/app.css',
                ],
            });
        },
        ordinal_suffix_of(i) {
            const j = i % 10, k = i % 100;
            if (j === 1 && k !== 11) return 'st';
            if (j === 2 && k !== 12) return 'nd';
            if (j === 3 && k !== 13) return 'rd';
            return 'th';
        },
    },
};
</script>

<style>
@media print {
    .no-print { display: none !important; }
    #printArea { border: none !important; box-shadow: none !important; }
    .card { border: none !important; }
    .card-body { padding: 0 !important; }
}
</style>
