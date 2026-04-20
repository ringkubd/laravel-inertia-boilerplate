<template>
    <Head>
        <title>Madrasah Notesheets</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Madrasah Notesheet Management</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <NavLink
                                    :href="route('madrasah_notesheet.create')"
                                    class="bg-green-600 hover:bg-green-800 px-4 py-2 rounded align-middle text-white text-capitalize font-bold font-sans"
                                >
                                    Generate Madrasah Notesheet
                                </NavLink>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Category</th>
                                <th>Session</th>
                                <th>Total Students</th>
                                <th>Page No</th>
                                <th>Serial No</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(ns, i) in notesheets.data" :key="ns.id">
                                <td>{{ i + 1 }}</td>
                                <td>{{ ns.notesheet_category }}</td>
                                <td>{{ ns.meta_data?.academic_session || '-' }}</td>
                                <td>{{ ns.meta_data?.total_students || '-' }}</td>
                                <td>{{ ns.page_no }}</td>
                                <td>{{ ns.serial_no }}</td>
                                <td>{{ ns.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <Paginator :paginator="notesheets"/>
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
import NavLink from "@/Components/NavLink";
import Paginator from "@/Components/Paginator.vue";

export default {
    name: "MadrasahNotesheetIndex",
    props: {
        notesheets: Object,
    },
    components: {Paginator, NavLink, CardHeader, PageHeader, Authenticated}
}
</script>
