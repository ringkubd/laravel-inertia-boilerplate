<template>
    <Head>
        <title>Placement Report</title>
    </Head>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Placement Report
            </h2>
        </template>
        <div class="container-fluid px-4 py-6">
            <!-- Filter Section -->
            <div class="card mb-6">
                <div class="card-header bg-gradient-to-r from-blue-500 to-blue-700 text-white p-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold">Filter Options</h3>
                </div>
                <div class="card-body p-6">
                    <form @submit.prevent="applyFilters" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Session Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Session
                                </label>
                                <select
                                    v-model="filters.session"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">All Sessions</option>
                                    <option v-for="session in sessions" :key="session" :value="session">
                                        {{ session }}
                                    </option>
                                </select>
                            </div>

                            <!-- Madrasah Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Madrasah
                                </label>
                                <select
                                    v-model="filters.madrasah_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">All Madrasahs</option>
                                    <option v-for="(madrasah, id) in madrasahs" :key="id" :value="id">
                                        {{ madrasah }}
                                    </option>
                                </select>
                            </div>

                            <!-- Polytechnic Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Polytechnic
                                </label>
                                <select
                                    v-model="filters.polytechnic_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">All Polytechnics</option>
                                    <option v-for="(polytechnic, id) in polytechnics" :key="id" :value="id">
                                        {{ polytechnic }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            >
                                Apply Filters
                            </button>
                            <button
                                type="button"
                                @click="resetFilters"
                                class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors"
                            >
                                Reset
                            </button>
                            <button
                                type="button"
                                @click="printReport"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors ml-auto"
                            >
                                <i class="fas fa-print mr-2"></i>Print Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ reportData.totalPlacements }}</div>
                        <div class="text-gray-600 text-sm mt-2">Total Placements</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ reportData.byStatus?.employment || 0 }}</div>
                        <div class="text-gray-600 text-sm mt-2">Employed</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ reportData.byStatus?.higher_study || 0 }}</div>
                        <div class="text-gray-600 text-sm mt-2">Higher Studies</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ reportData.byStatus?.other || 0 }}</div>
                        <div class="text-gray-600 text-sm mt-2">Other</div>
                    </div>
                </div>
            </div>

            <!-- Session-Level Statistics Table -->
            <div class="card mb-6">
                <div class="card-header bg-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800">Session Statistics</h3>
                </div>
                <div class="card-body p-4 table-responsive">
                    <table class="w-full text-sm text-left text-gray-800">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border">Session</th>
                            <th class="px-4 py-2 border">Madrasah Starters</th>
                            <th class="px-4 py-2 border">Pass</th>
                            <th class="px-4 py-2 border">Fail</th>
                            <th class="px-4 py-2 border">Polytechnic Admits</th>
                            <th class="px-4 py-2 border">Dropouts</th>
                            <th class="px-4 py-2 border">Completed/Continuing</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(stat, session) in sessionStats" :key="session">
                            <td class="px-4 py-2 border">{{ session }}</td>
                            <td class="px-4 py-2 border">{{ stat.madrasa_starters }}</td>
                            <td class="px-4 py-2 border">{{ stat.madrasa_pass }}</td>
                            <td class="px-4 py-2 border">{{ stat.madrasa_fail }}</td>
                            <td class="px-4 py-2 border">{{ stat.polytechnic_admit }}</td>
                            <td class="px-4 py-2 border">{{ stat.polytechnic_dropout }}</td>
                            <td class="px-4 py-2 border">{{ stat.polytechnic_complete }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Status Distribution Chart -->
                <div class="card">
                    <div class="card-header bg-blue-50 p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Placement Status Distribution</h3>
                    </div>
                    <div class="card-body p-4">
                        <canvas
                            ref="statusChartCanvas"
                            id="statusChart"
                            height="220"
                            class="max-h-60 w-full"
                        ></canvas>
                    </div>
                </div>

                <!-- Salary Range Chart -->
                <div class="card">
                    <div class="card-header bg-blue-50 p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Salary Distribution</h3>
                    </div>
                    <div class="card-body p-4">
                        <canvas
                            ref="salaryChartCanvas"
                            id="salaryChart"
                            height="220"
                            class="max-h-60 w-full"
                        ></canvas>
                    </div>
                </div>

                <!-- Madrasah Distribution Chart -->
                <div class="card">
                    <div class="card-header bg-blue-50 p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Placements by Madrasah</h3>
                    </div>
                    <div class="card-body p-4">
                        <canvas
                            ref="madrasahChartCanvas"
                            id="madrasahChart"
                            height="220"
                            class="max-h-60 w-full"
                        ></canvas>
                    </div>
                </div>

                <!-- Session Distribution Chart -->
                <div class="card">
                    <div class="card-header bg-blue-50 p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Placements by Session</h3>
                    </div>
                    <div class="card-body p-4">
                        <canvas
                            ref="sessionChartCanvas"
                            id="sessionChart"
                            height="220"
                            class="max-h-60 w-full"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Template (Hidden) -->
        <div id="printArea" style="display: none;">
            <div class="print-content">
                <h1 style="text-align: center; margin-bottom: 20px;">Placement Report</h1>

                <div style="margin-bottom: 30px;">
                    <h3>Summary Statistics</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 10px;">Total Placements: <strong>{{ reportData.totalPlacements }}</strong></td>
                            <td style="border: 1px solid #ddd; padding: 10px;">Employed: <strong>{{ reportData.byStatus?.employment || 0 }}</strong></td>
                            <td style="border: 1px solid #ddd; padding: 10px;">Higher Studies: <strong>{{ reportData.byStatus?.higher_study || 0 }}</strong></td>
                            <td style="border: 1px solid #ddd; padding: 10px;">Other: <strong>{{ reportData.byStatus?.other || 0 }}</strong></td>
                        </tr>
                    </table>
                </div>

                <div style="margin-bottom: 30px;">
                    <h3>Session Statistics</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="border: 1px solid #ddd; padding: 8px;">Session</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Madrasah Starters</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Pass</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Fail</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Polytechnic Admits</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Dropouts</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Completed/Continue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(stat, session) in sessionStats" :key="session">
                                <td class="px-4 py-2 border">{{ session }}</td>
                                <td class="px-4 py-2 border">{{ stat.madrasa_starters }}</td>
                                <td class="px-4 py-2 border">{{ stat.madrasa_pass }}</td>
                                <td class="px-4 py-2 border">{{ stat.madrasa_fail }}</td>
                                <td class="px-4 py-2 border">{{ stat.polytechnic_admit }}</td>
                                <td class="px-4 py-2 border">{{ stat.polytechnic_dropout }}</td>
                                <td class="px-4 py-2 border">{{ stat.polytechnic_complete }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

                <div style="margin-bottom: 30px;">
                    <h3>Placement Details</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                        <tr style="background-color: #f5f5f5;">
                            <th style="border: 1px solid #ddd; padding: 8px;">Student</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Madrasah</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="placement in placements" :key="placement.id">
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ placement.student?.name }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ placement.student?.madrasha?.name }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ getStatusText(placement.present_status_type) }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px; font-size: 10px;">
                                <span v-if="getStatusText(placement.present_status_type) === 'Employed'">
                                    {{ placement.status?.organization }} - {{ placement.status?.position }}
                                </span>
                                <span v-else-if="getStatusText(placement.present_status_type) === 'Higher Study'">
                                    {{ placement.status?.institute_name }}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 30px; text-align: right; font-size: 12px;">
                    <p>Generated on: {{ new Date().toLocaleDateString() }}</p>
                </div>
            </div>

    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import { Head, router } from "@inertiajs/vue3";
import Chart from 'chart.js/auto';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },
    props: {
        placements: Array,
        reportData: Object,
        sessionStats: Object, // additional statistics by session
        sessions: Array,
        madrasahs: Object,
        polytechnics: Object,
        filters: Object,
    },
    data() {
        return {
            filters: {
                session: this.$props.filters?.session || '',
                madrasah_id: this.$props.filters?.madrasah_id || '',
                polytechnic_id: this.$props.filters?.polytechnic_id || '',
            },
            charts: {
                statusChart: null,
                salaryChart: null,
                madrasahChart: null,
                sessionChart: null,
            }
        };
    },
    mounted() {
        this.initializeCharts();
    },
    methods: {
        applyFilters() {
            router.get(route('placement.report'), this.filters, {
                preserveState: false,
            });
        },
        resetFilters() {
            this.filters = {
                session: '',
                madrasah_id: '',
                polytechnic_id: '',
            };
            this.applyFilters();
        },
        initializeCharts() {
            this.$nextTick(() => {
                this.renderStatusChart();
                this.renderSalaryChart();
                this.renderMadrasahChart();
                this.renderSessionChart();
            });
        },
        renderStatusChart() {
            const ctx = this.$refs.statusChartCanvas;
            if (!ctx) return;

            if (this.charts.statusChart) {
                this.charts.statusChart.destroy();
            }

            const data = {
                labels: ['Employed', 'Higher Study', 'Other'],
                datasets: [{
                    data: [
                        this.reportData.byStatus?.employment || 0,
                        this.reportData.byStatus?.higher_study || 0,
                        this.reportData.byStatus?.other || 0,
                    ],
                    backgroundColor: [
                        '#10B981',
                        '#8B5CF6',
                        '#F59E0B',
                    ],
                    borderColor: [
                        '#059669',
                        '#6D28D9',
                        '#D97706',
                    ],
                    borderWidth: 2,
                }],
            };

            this.charts.statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                    scales: {},
                },
            });
        },
        renderSalaryChart() {
            const ctx = this.$refs.salaryChartCanvas;
            if (!ctx) return;

            if (this.charts.salaryChart) {
                this.charts.salaryChart.destroy();
            }

            const data = {
                labels: ['Below 20K', '20K-40K', '40K-60K', '60K-100K', 'Above 100K'],
                datasets: [{
                    label: 'Number of Employees',
                    data: [
                        this.reportData.salaryRanges?.below_20k || 0,
                        this.reportData.salaryRanges?.['20k_40k'] || 0,
                        this.reportData.salaryRanges?.['40k_60k'] || 0,
                        this.reportData.salaryRanges?.['60k_100k'] || 0,
                        this.reportData.salaryRanges?.above_100k || 0,
                    ],
                    backgroundColor: '#3B82F6',
                    borderColor: '#1E40AF',
                    borderWidth: 1,
                }],
            };

            this.charts.salaryChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        },
        renderMadrasahChart() {
            const ctx = this.$refs.madrasahChartCanvas;
            if (!ctx) return;

            if (this.charts.madrasahChart) {
                this.charts.madrasahChart.destroy();
            }

            const labels = Object.keys(this.reportData.byMadrasah || {});
            const data = Object.values(this.reportData.byMadrasah || {});

            const data_config = {
                labels: labels,
                datasets: [{
                    label: 'Placements',
                    data: data,
                    backgroundColor: '#EC4899',
                    borderColor: '#BE185D',
                    borderWidth: 1,
                }],
            };

            this.charts.madrasahChart = new Chart(ctx, {
                type: 'bar',
                data: data_config,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: true,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        },
        renderSessionChart() {
            const ctx = this.$refs.sessionChartCanvas;
            if (!ctx) return;

            if (this.charts.sessionChart) {
                this.charts.sessionChart.destroy();
            }

            const labels = Object.keys(this.reportData.bySession || {});
            const data = Object.values(this.reportData.bySession || {});

            const data_config = {
                labels: labels,
                datasets: [{
                    label: 'Placements',
                    data: data,
                    backgroundColor: '#06B6D4',
                    borderColor: '#0891B2',
                    borderWidth: 1,
                }],
            };

            this.charts.sessionChart = new Chart(ctx, {
                type: 'bar',
                data: data_config,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        },
        getStatusText(statusType) {
            if (statusType.includes('Employment')) {
                return 'Employed';
            } else if (statusType.includes('FurtherEducation')) {
                return 'Higher Study';
            } else {
                return 'Other';
            }
        },
        getStatusBadgeClass(statusType) {
            if (statusType.includes('Employment')) {
                return 'bg-green-100 text-green-800';
            } else if (statusType.includes('FurtherEducation')) {
                return 'bg-purple-100 text-purple-800';
            } else {
                return 'bg-orange-100 text-orange-800';
            }
        },
        printReport() {
            const printWindow = window.open('', '', 'height=600,width=800');
            const printContent = document.getElementById('printArea').innerHTML;

            printWindow.document.write(`
                <html>
                <head>
                    <title>Placement Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #333; border-bottom: 2px solid #3B82F6; padding-bottom: 10px; }
                        h3 { color: #555; margin-top: 20px; }
                        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        th { background-color: #f5f5f5; font-weight: bold; }
                        tr:nth-child(even) { background-color: #f9f9f9; }
                        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
                        .summary-item { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
                        .summary-value { font-size: 24px; font-weight: bold; color: #3B82F6; }
                        .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                    </style>
                </head>
                <body>
                    <h1>Placement Report</h1>
                    <div class="summary">
                        <div class="summary-item">
                            <div class="summary-value">${this.reportData.totalPlacements}</div>
                            <div class="summary-label">Total Placements</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${this.reportData.byStatus?.employment || 0}</div>
                            <div class="summary-label">Employed</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${this.reportData.byStatus?.higher_study || 0}</div>
                            <div class="summary-label">Higher Studies</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${this.reportData.byStatus?.other || 0}</div>
                            <div class="summary-label">Other</div>
                        </div>
                    </div>

                    <h3>Placement Details</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Madrasah</th>
                                <th>Polytechnic</th>
                                <th>Session</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${this.placements.map(placement => `
                                <tr>
                                    <td>${placement.student?.name}</td>
                                    <td>${placement.student?.madrasha?.name}</td>
                                    <td>${placement.student?.polytechnic_info?.name}</td>
                                    <td>${placement.student?.polytechnic_session}</td>
                                    <td>${this.getStatusText(placement.present_status_type)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <p style="margin-top: 30px; text-align: right; color: #666; font-size: 12px;">
                        Generated on: ${new Date().toLocaleDateString()}
                    </p>
                </body>
                </html>
            `);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }
    },
    watch: {
        reportData() {
            this.initializeCharts();
        }
    }
};
</script>

<style scoped>
.card {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem;
}

.card-body {
    padding: 1.5rem;
}

.table-responsive {
    max-width: 100%;
    overflow-x: auto;
}

@media print {
    .btn, .filter-section {
        display: none;
    }

    canvas {
        page-break-inside: avoid;
    }
}
</style>
