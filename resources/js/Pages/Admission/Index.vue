<template>
    <Head>
        <title>Admission Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Admission Management</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card flex min-h-screen justify-center mt-5">
                    <div class="card-header">
                        <card-header
                            :create="route('admission.create')"
                            :searchMethod="search"
                            :can="can"
                        ></card-header>
                    </div>
                    <div class="card-body">
                        <table class="table table-secondary table-striped">
                            <thead>
                            <tr>
                                <th>SL#</th>
                                <th>Name</th>
                                <th>Madrasah</th>
                                <th>Session</th>
                                <th>Tracking ID</th>
                                <th>Admission Fee</th>
                                <th>Money Receipt</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(admission, index) in admissions.data">
                                <td>{{ index+1 }}</td>
                                <td>{{ admission?.student?.name }}</td>
                                <td>{{ admission?.student?.madrasha?.name }}</td>
                                <td>{{ admission?.academic_session }}</td>
                                <td>{{ admission?.tracking_id }}</td>
                                <td>{{ admission?.admission_fee }}</td>
                                <td>{{ admission?.money_receipt }}</td>
                                <td>{{ admission?.status }}</td>
                                <td>
                                    <Actions
                                        :delete-url="route('admission.destroy', admission.id)"
                                        :detail-url="route('admission.show', admission.id)"
                                        :edit-url="route('admission.edit', admission.id)"
                                        :can="can"
                                    ></Actions>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <Paginator :paginator="admissions" />
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
export default {
    name: "Index",
    components: {Paginator, Actions, CardHeader, PageHeader, Authenticated},
    props: ['admissions', 'can'],
    data(){

    },
    methods: {
        search(param){
            this.$inertia.replace(route('admission.index', { search: param }))
        }
    }
}
</script>

<style scoped>

</style>
