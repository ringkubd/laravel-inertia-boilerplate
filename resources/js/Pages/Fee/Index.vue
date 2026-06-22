<template>
    <Head>
        <title>Fee's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <PageHeader>Fee Information</PageHeader>
        </template>
        <div class="container-fluid py-3">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200">
                    <CardHeader :can="can" :create="route('fee.create')" :search-method="search">
                        <template #first>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Session</label>
                                <select v-model="filterParam.session" class="form-control" @change="applyFilter">
                                    <option value="">All Sessions</option>
                                    <option v-for="s in sessions" :value="s">{{ s }}</option>
                                </select>
                            </div>
                        </template>
                        <template #second>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Trade</label>
                                <select v-model="filterParam.trade" class="form-control" @change="applyFilter">
                                    <option value="">All Trades</option>
                                    <option v-for="t in trades" :value="t">{{ t }}</option>
                                </select>
                            </div>
                        </template>
                        <template #third>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
                                <select v-model="filterParam.semester" class="form-control" @change="applyFilter">
                                    <option value="">All Semesters</option>
                                    <option v-for="sem in semesters" :value="sem">{{ sem }}{{ ordinal_suffix_of(sem) }}</option>
                                </select>
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SL#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Session</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trade</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Semester</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(fee, index) in data.data" :key="fee.id" class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ index + 1 + (data.current_page - 1) * data.per_page }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ fee.session }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ fee.trade }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ fee.semester }}{{ ordinal_suffix_of(fee.semester) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ fee.fee_type }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ Number(fee.amount).toLocaleString() }}</td>
                                <td class="px-4 py-3 text-center">
                                    <Actions :can="can" :delete-url="route('fee.destroy', fee.id)" :edit-url="route('fee.edit', fee.id)"></Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="data.data && data.data.length" class="border-t border-gray-200 px-4 py-3 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            Showing {{ data.from }} to {{ data.to }} of {{ data.total }} entries
                        </span>
                        <span class="text-sm font-semibold text-gray-700">
                            Total: {{ totalAmount.toLocaleString() }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-3">
                    <Paginator :paginator="data"/>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
import PageHeader from "@/Shared/PageHeader";
export default {
    components: {
        Paginator,
        Actions,
        CardHeader,
        AppLayout,
        PageHeader,
    },
    props: ['data', 'errors', 'flash', 'can', 'filters', 'sessions', 'trades', 'semesters', 'fee_types'],
    data(){
        return {
            filterParam: {
                search: this.filters?.search || '',
                session: this.filters?.session || '',
                trade: this.filters?.trade || '',
                semester: this.filters?.semester || '',
                fee_type: this.filters?.fee_type || '',
            }
        }
    },
    methods: {
        search(query){
            this.filterParam.search = query;
            this.applyFilter();
        },
        applyFilter(){
            this.$inertia.get(route('fee.index'), this.filterParam, { preserveState: true });
        },
        ordinal_suffix_of(i){
            const j = i % 10, k = i % 100;
            if (j === 1 && k !== 11) return 'st';
            if (j === 2 && k !== 12) return 'nd';
            if (j === 3 && k !== 13) return 'rd';
            return 'th';
        },
    },
    computed: {
        totalAmount() {
            return this.data.data.reduce((sum, fee) => sum + Number(fee.amount), 0);
        },
    },
}
</script>
