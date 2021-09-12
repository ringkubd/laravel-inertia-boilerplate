<template>
    <Head>
        <title>Teacher's Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Teacher's Management</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('teacher.create')" :SearchMethod="search">
                        <template #first>
                            dd
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>Sl#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>NID</th>
                            <th>Joining Date</th>
                            <th>Designation</th>
                            <th>Trade</th>
                            <th>Madrasa</th>
                            <th>DOB</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(teacher, index) in teachers.data">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ teacher.photo }}
                                </td>
                                <td>
                                    {{ teacher.name }}
                                </td>
                                <td>
                                    {{ teacher.user.email }}
                                </td>
                                <td>
                                    {{ teacher.mobile }}
                                </td>
                                <td>
                                    {{ teacher.NID }}
                                </td>
                                <td>
                                    {{ teacher.joining_date }}
                                </td>
                                <td>
                                    {{ teacher.designation }}
                                </td>
                                <td>
                                    {{ teacher.trade ? teacher.trade.name : "" }}
                                </td>
                                <td>
                                    {{ teacher.madrasa ? teacher.madrasa.name :"" }}
                                </td>
                                <td>
                                    {{ teacher.dob }}
                                </td>
                                <td>
                                    <Actions :can="can" :delete-url="route('teacher.destroy', teacher.id)" :edit-url="route('teacher.edit', teacher.id)"></Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <paginator :paginator="teachers"></paginator>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
export default {
    name: "Index",
    components: {Paginator, Actions, CardHeader, Authenticated},
    props: ['can', 'teachers'],
    data(){
        return {
            filterParam: {
                trade: null,
                session: null
            }
        }
    },
    methods: {
        search(query){
            this.$inertia.replace(route('teacher.index', {'search': query}))
        },
        filterData(){
            this.$inertia.replace(route('teacher.index', {'current_session': this.filterParam.session, 'trade': this.filterParam.trade}))
        }
    },
    mounted() {
        console.log(this.students)
    }
}
</script>

<style scoped>

</style>
