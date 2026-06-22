<template>
    <Head>
        <title>Fee's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fee Information</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('fee.create')" :search-method="search">
                        <template #first>
                            <div class="form-group row">
                                <label for="filter_session" class="col-sm-3 col-form-label">Session</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.session" name="session" id="filter_session" class="form-control" @change="applyFilter">
                                        <option value="">All</option>
                                        <option v-for="s in sessions" :value="s">{{ s }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <template #second>
                            <div class="form-group row">
                                <label for="filter_trade" class="col-sm-3 col-form-label">Trade</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.trade" name="trade" id="filter_trade" class="form-control" @change="applyFilter">
                                        <option value="">All</option>
                                        <option v-for="t in trades" :value="t">{{ t }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <template #third>
                            <div class="form-group row">
                                <label for="filter_semester" class="col-sm-3 col-form-label">Semester</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.semester" name="semester" id="filter_semester" class="form-control" @change="applyFilter">
                                        <option value="">All</option>
                                        <option v-for="sem in semesters" :value="sem">{{ sem }}{{ ordinal_suffix_of(sem) }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>SL#</th>
                            <th>Session</th>
                            <th>Trade</th>
                            <th>Semester</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(fee, index) in data.data" :key="fee.id">
                            <th>{{ index + 1 + (data.current_page - 1) * data.per_page }}</th>
                            <td>{{ fee.session }}</td>
                            <td>{{ fee.trade }}</td>
                            <td>{{ fee.semester }}{{ ordinal_suffix_of(fee.semester) }}</td>
                            <td>{{ fee.fee_type }}</td>
                            <td>{{ fee.amount }}</td>
                            <td>
                                <Actions :can="can" :delete-url="route('fee.destroy', fee.id)" :edit-url="route('fee.edit', fee.id)"></Actions>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator  :paginator="data"/>
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
export default {
    components: {
        Paginator,
        Actions,
        CardHeader,
        AppLayout
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
}
</script>
