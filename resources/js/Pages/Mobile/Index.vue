<template>
    <Head>
        <title>Mobile Application Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>Mobile Application Management</h2>
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader
                            :create="route('mobile.create')"
                            :can="can"
                        />
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-secondary table-striped">
                            <thead>
                            <tr>
                                <th>SL#</th>
                                <th>Name</th>
                                <th>Platform</th>
                                <th>Version</th>
                                <th>File/Link</th>
                                <th>Downloads</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(app, index) in applications">
                                <td>{{ index + 1 }}</td>
                                <td>{{ app.name }}</td>
                                <td>{{ app.platform }}</td>
                                <td>{{ app.version_code }}</td>
                                <td>{{ app.file_path }}</td>
                                <td>{{ app.number_of_download }}</td>
                                <td>{{ app.status == 1 ? 'Published' : 'Pending' }}</td>
                                <td>
                                    <Actions
                                        :can="can"
                                        :edit-url="route('mobile.edit', app.id)"
                                        :delete-url="route('mobile.destroy', app.id)"
                                    />
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
import Actions from "@/Shared/Actions";
export default {
    name: "Index",
    props: ['applications', 'can'],
    components: {
        Actions,
        CardHeader,
        PageHeader,
        Authenticated
    },
    methods(){
        const addMobileApp = () => {

        }
    }
}
</script>

<style scoped>

</style>
