<template>
    <Head>
        <title>Financial Report</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Financial Report</PageHeader>
        </template>
        <div class="container-fluid py-3">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
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

                    <div class="flex flex-wrap gap-3 mb-6 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                            <select v-model="filters.session" class="form-control" @change="applyFilter">
                                <option value="">All Sessions</option>
                                <option v-for="s in sessions" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" v-model="filters.from_date" class="form-control" @change="applyFilter">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" v-model="filters.to_date" class="form-control" @change="applyFilter">
                        </div>
                        <div class="flex items-end">
                            <button @click="resetFilter" class="btn btn-warning">Reset</button>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 mb-4">
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
                                    <th>Total Amount</th>
                                    <th>Students Paid</th>
                                    <th>Avg/Student</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in monthly" :key="i">
                                    <td>{{ row.month }}</td>
                                    <td class="text-right">{{ Number(row.total_amount).toLocaleString() }}</td>
                                    <td class="text-center">{{ row.students_paid }}</td>
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
                                        <th>Total Received</th>
                                        <th>Months Active</th>
                                        <th>Avg Monthly</th>
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
                                    <th>Total Amount</th>
                                    <th>Students</th>
                                    <th>Avg/Student</th>
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
                                    <th>Total Amount</th>
                                    <th>Students</th>
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
            filters: {
                session: this.filters?.session || '',
                from_date: this.filters?.from_date || '',
                to_date: this.filters?.to_date || '',
            },
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
            this.$inertia.get(route('financial.report'), this.filters, { preserveState: true });
        },
        resetFilter() {
            this.$inertia.get(route('financial.report'), {}, { preserveState: true });
        },
    },
};
</script>
