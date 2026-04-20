<template>
    <Head>
        <title>Notesheet Templates</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Notesheet Template Management</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <NavLink :href="route('note_sheet_template.create')" class="bg-green-600 hover:bg-green-800 px-4 py-2 rounded text-white font-bold">
                                    Create Template
                                </NavLink>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Title</th>
                                <th>Fee Types</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, i) in note_sheet_templates" :key="item.id">
                                <td>{{ i + 1 }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ (item.fee_types || []).map(f => f.name || f.label).join(', ') || '-' }}</td>
                                <td>{{ item.created_at }}</td>
                                <td>
                                    <NavLink :href="route('note_sheet_template.edit', item.id)" class="btn btn-sm btn-info mr-2">Edit</NavLink>
                                    <button type="button" class="btn btn-sm btn-danger" @click="destroy(item.id)">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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

export default {
    name: 'NoteSheetTemplateIndex',
    props: {
        note_sheet_templates: Array,
        can: Object,
    },
    components: {NavLink, CardHeader, PageHeader, Authenticated},
    methods: {
        destroy(id) {
            if (!confirm('Delete this template?')) return;
            this.$inertia.delete(route('note_sheet_template.destroy', id));
        }
    }
}
</script>
