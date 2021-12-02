<template>
    <Head>
        <title>Student Management</title>
        <meta property="og:title" content="Student Management" />
        <meta property="og:description" content="IsDB-BISEW Four Year Diploma Student Management." />
        <meta property="og:image" content="https://www.isdb-bisew.org/img/isdb-bisew.png" />
    </Head>
    <Authenticated>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Management</h2>
            <div class="form-group row">
                <label for="madrasah" class="col-sm-3 col-form-label">Madrasah</label>
                <div class="col-sm-9">
                    <select v-model="filterParam.madrasah" name="madrasah" id="madrasah" class="form-control" @change="filterData">
                        <option value=""></option>
                        <option v-for="madrasah in madrasahs" :value="madrasah.id">{{madrasah.name}}</option>
                    </select>
                </div>
            </div>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('polytechnic.student.create')" :search-method="search">
                        <template #first>
                            <div class="form-group row">
                                <label for="trade" class="col-sm-3 col-form-label">Trade</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.trade" name="trade" id="trade" class="form-control" @change="filterData">
                                        <option value=""></option>
                                        <option v-for="trade in trades" :value="trade.name">{{trade.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <template #second>
                            <div class="form-group row">
                                <label for="session" class="col-sm-3 col-form-label">Session</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.session" name="session" id="session" class="form-control" @change="filterData">
                                        <option value=""></option>
                                        <option v-for="session in academic_session" :value="session.session">{{session.session}}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <template #third>
                            <div class="form-group row">
                                <label for="classroom" class="col-sm-3 col-form-label">Class</label>
                                <div class="col-sm-9">
                                    <select v-model="filterParam.classroom" name="classroom" id="classroom" class="form-control" @change="filterData">
                                        <option value=""></option>
                                        <option v-for="cl in classes" :value="cl.id">{{cl.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-secondary text-center">
                        <thead>
                        <tr>
                            <th>Sl#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>S.ID</th>
                            <th>C. Roll</th>
                            <th>Session</th>
                            <th>Mobile</th>
                            <th>Father</th>
                            <th>Trade</th>
                            <th>Madrasa</th>
                            <th>Polytechnic</th>
                            <th>Semester</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(student,index) in students.data">
                            <td>{{index + 1}}</td>
                            <td>
                                <img v-if="student.photo" :src="'/'+student.photo" width="50" :alt="student.name">
                            </td>
                            <td>
                                <a :href="route('madrasa.student.show', student.id)" _target="blank">
                                    {{student.name}}
                                </a>
                            </td>
                            <td>
                                {{ student.student_id }}
                            </td>
                            <td>
                                {{ student.class_roll }}
                            </td>
                            <td>
                                {{ student.current_session }}
                            </td>
                            <td>
                                {{ student.mobile }}
                            </td>
                            <td>
                                {{ student.father_name }}
                            </td>
                            <td>
                                {{ student.polytechnic_trade_id }}
                            </td>
                            <td>
                                {{student.madrasha ? student.madrasha.name : ""}}
                            </td>
                            <td>
                                {{student.polytechnic ? student.polytechnic.name : ""}}
                            </td>
                            <td>
                                {{ student.semester }}
                            </td>
                            <td>
                                <Actions :can="can" :deleteUrl="route('polytechnic.student.destroy', student.id)" :editUrl="route('polytechnic.student.edit', student.id)" :isDetails="true" :detailUrl="route('madrasa.student.show', student.id)"></Actions>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <paginator :paginator="students"></paginator>
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
    props: ['students', 'trades', 'academic_session', 'can', 'classes', 'madrasahs', 'selected_madrasah'],
    data(){
        return {
            filterParam: {
                trade: GET('trade')[0],
                session: GET('current_session')[0],
                classroom: GET('classroom')[0],
                madrasah: this.selected_madrasah
            }
        }
    },
    methods: {
        search(query){
            this.$inertia.replace(route('polytechnic.student.index', {'search': query}))
        },
        filterData(){
            this.$inertia.replace(route('polytechnic.student.index', { 'madrasah' : this.filterParam.madrasah, 'current_session': this.filterParam.session, 'trade': this.filterParam.trade, 'classroom': this.filterParam.classroom}))
        }
    },
    mounted() {
        console.log(this.students)
    }
}
</script>

<style scoped>

</style>
