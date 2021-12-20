<template>
    <Head>
        <title>Result's Management</title>
    </Head>
    <app-layout>
        <template #header>
            <PageHeader>Result's Management</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('madrasa.result.create')" :search-method="search">
                        <template #first>
                            <div class="flex">
                                <label class="w-25" for="session">Session</label>
                                <select2 class="w-75" id="session" name="session" :options="academicSession" v-model="academic_session" @select="filter" />
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body">
                    <table class="table table-secondary table-striped text-center">
                        <thead>
                        <tr>
                            <th>SL.#</th>
                            <th>Name</th>
                            <th>Session</th>
                            <th>Trade</th>
                            <th>Class Nine GPA</th>
                            <th>Dakhil Result</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(result, index) in results.data" :key="result.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ result.student?.name }}</td>
                            <td>{{ result.student?.ssc_session }}</td>
                            <td>{{ result?.student?.madrasa_trade_id }}</td>
                            <td>{{ result.nine_gpa }}</td>
                            <td>{{ result.ten_gpa }}</td>
                            <td>{{ result.status }}</td>
                            <td>
                                <Actions :can="can" :delete-url="route('madrasa.result.destroy', result.id)" :edit-url="route('madrasa.result.edit', result.id)"></Actions>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator :paginator="results" />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import PageHeader from "@/Shared/PageHeader";
import Paginator from "@/Components/Paginator";
export default {
    components: {
        Paginator,
        PageHeader,
        Actions,
        CardHeader,
        AppLayout
    },
    props: ['results', 'errors', 'flash', 'can', 'academicSession'],
    data(){
        return {
            academic_session: "",
            academicSession: this.academicSession
        }
    },
    methods: {
        search(searchItem){
            this.$inertia.replace(route('madrasa.result.index', {'search': searchItem}))
        },
        filter(e){
            this.search(this.academic_session)
        }
    },
    computed:{
        academicSession(){
            return this.academicSession.filter((session) => {
                return session['id'] = session.text
            })
        }
    }
}
</script>

<style scoped>

</style>
