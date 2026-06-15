<template>
    <Head>
        <title>Polytechnic Student Report</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Polytechnic Student Report</PageHeader>
        </template>
        <div class="container-fluid py-3">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-brand-600">{{ summary.total }}</div>
                            <div class="text-sm text-gray-500 mt-1">Total Students</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-accent-600">{{ summary.continuing }}</div>
                            <div class="text-sm text-gray-500 mt-1">Continuing</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-red-600">{{ summary.dropout }}</div>
                            <div class="text-sm text-gray-500 mt-1">Dropout</div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ summary.suspended }}</div>
                            <div class="text-sm text-gray-500 mt-1">Suspended</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-6 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                            <select v-model="filters.session" class="form-control">
                                <option value="">All Sessions</option>
                                <option v-for="s in sessions" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Polytechnic</label>
                            <select v-model="filters.polytechnic" class="form-control">
                                <option value="">All Polytechnics</option>
                                <option v-for="p in polytechnics" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trade</label>
                            <select v-model="filters.trade" class="form-control">
                                <option value="">All Trades</option>
                                <option v-for="t in trades" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button @click="applyFilter" class="btn btn-success">Apply</button>
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

                    <div v-if="activeTab === 'session'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Total</th>
                                    <th>Continuing</th>
                                    <th>Dropout</th>
                                    <th>Suspended</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, session) in bySession" :key="session">
                                    <td>{{ session }}</td>
                                    <td class="text-center">{{ data.total }}</td>
                                    <td class="text-center text-accent-600 font-medium">{{ data.continuing }}</td>
                                    <td class="text-center text-red-600 font-medium">{{ data.dropout }}</td>
                                    <td class="text-center text-yellow-600 font-medium">{{ data.suspended }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'polytechnic'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Polytechnic</th>
                                    <th>Total</th>
                                    <th>Continuing</th>
                                    <th>Dropout</th>
                                    <th>Suspended</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, polytechnic) in byPolytechnic" :key="polytechnic">
                                    <td>{{ polytechnic }}</td>
                                    <td class="text-center">{{ data.total }}</td>
                                    <td class="text-center text-accent-600 font-medium">{{ data.continuing }}</td>
                                    <td class="text-center text-red-600 font-medium">{{ data.dropout }}</td>
                                    <td class="text-center text-yellow-600 font-medium">{{ data.suspended }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'semester'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Semester</th>
                                    <th>Total</th>
                                    <th>Continuing</th>
                                    <th>Dropout</th>
                                    <th>Suspended</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, semester) in bySemester" :key="semester">
                                    <td>{{ ordinal_suffix_of(semester) }}</td>
                                    <td class="text-center">{{ data.total }}</td>
                                    <td class="text-center text-accent-600 font-medium">{{ data.continuing }}</td>
                                    <td class="text-center text-red-600 font-medium">{{ data.dropout }}</td>
                                    <td class="text-center text-yellow-600 font-medium">{{ data.suspended }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'trade'" class="table-responsive">
                        <table class="table table-striped table-secondary">
                            <thead>
                                <tr>
                                    <th>Trade</th>
                                    <th>Total</th>
                                    <th>Continuing</th>
                                    <th>Dropout</th>
                                    <th>Suspended</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, trade) in byTrade" :key="trade">
                                    <td>{{ trade }}</td>
                                    <td class="text-center">{{ data.total }}</td>
                                    <td class="text-center text-accent-600 font-medium">{{ data.continuing }}</td>
                                    <td class="text-center text-red-600 font-medium">{{ data.dropout }}</td>
                                    <td class="text-center text-yellow-600 font-medium">{{ data.suspended }}</td>
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
        bySession: Object,
        byPolytechnic: Object,
        bySemester: Object,
        byTrade: Object,
        filters: Object,
        sessions: Array,
        polytechnics: Array,
        trades: Array,
    },
    data() {
        return {
            filters: { ...this.filters },
            activeTab: 'session',
            tabs: [
                { key: 'session', label: 'Session-wise' },
                { key: 'polytechnic', label: 'Polytechnic-wise' },
                { key: 'semester', label: 'Semester-wise' },
                { key: 'trade', label: 'Trade-wise' },
            ],
        };
    },
    methods: {
        applyFilter() {
            this.$inertia.get(route('polytechnic.report'), this.filters, { preserveState: true });
        },
    },
};
</script>
