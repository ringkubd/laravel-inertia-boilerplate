<template>
    <Head>
        <title>Note Sheet Template</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                Note Sheet Template
            </PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <CardHeader :create="route('note_sheet_template.create')" :can="can" :search-method="search"></CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-secondary">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Title</th>
                            <th>Group</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(nst, index) in note_sheet_templates">
                            <td>{{ index + 1 }}</td>
                            <td>{{ nst.title }}</td>
                            <td>{{ group(nst.fee_types) }}</td>
                            <td v-html="nst.content"></td>
                            <td>
                                <Actions :can="can" :delete-url="route('note_sheet_template.destroy', nst.id)" :edit-url="route('note_sheet_template.edit', nst.id)"/>
                            </td>
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
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
export default {
    name: "Index",
    props: ['can', 'note_sheet_templates'],
    components: {Actions, CardHeader, PageHeader, Authenticated},
    methods: {
        search(){

        },
        group(feeTypes){
            var groups = ""
            feeTypes.forEach((fee) => {
                groups +=fee.name+","
            })
            return groups;
        }

    }
}
</script>

<style scoped>

</style>
