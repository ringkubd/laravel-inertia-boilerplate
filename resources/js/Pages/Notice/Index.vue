<template>
    <Head>
        <title>Notice's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notices</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('notice.create')" :search-method="search"></CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>SL#</th>
                            <th>Session</th>
                            <th>Class</th>
                            <th>Title</th>
                            <th>Body</th>
                            <th>Published</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(notice, index) in notices.data" :key="notice.id">
                            <th>{{index + 1}}</th>
                            <td>{{notice.academic_session}}</td>
                            <td>{{notice.class_room?.name}}</td>
                            <td>{{notice.title}}</td>
                            <td>{{notice.body}}</td>
                            <td>{{notice.published_at}}</td>
                            <td>
                                <Actions :can="can" :delete-url="route('notice.destroy', notice.id)" :edit-url="route('notice.edit', notice.id)"></Actions>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator  :paginator="notices"/>
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
    props: ['notices', 'errors', 'flash', 'can'],
    data(){
        return {
            form: {
                name : null,
                district : null,
                address : null,
                telephone : null,
                mobile : null,
                email : null,
                fax : null,
            },
        }
    },
    methods: {
        search(searchItem){
            this.$inertia.replace(route('notice.index', {'search': searchItem}))
        }
    },
}
</script>

<style scoped>

</style>
