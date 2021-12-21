<template>
    <Head>
        <title>Result Management System</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Result's Management</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :create="false" :search-method="search">
                        <template #first>
                            <div class="flex">
                                <label for="session" class="col-3">Session</label>
                                <div class="col-8">
                                    <select name="session" id="session" class="form-control" v-model="filters.academic_session" @change.prevent="filteredSessions">
                                        <option v-for="session in sessions" :value="session.session">{{session.session}}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body overflow-x-auto w-full">
                    <div class="my-4">
                        <form action="" class="form-inline bg-gradient-to-tr from-green-500 via-green-200 to-green-400 bg-blend-color-dodge" @submit.prevent="submitForm">
                            <div class="row pl-2 py-4">
                                <div class="col form-group">
                                    <label for="student">Student</label>
                                    <Multiselect v-model="newResult.student_id"
                                                 :filterResults="true"
                                                 :minChars="1"
                                                 :delay="0"
                                                 :searchable="true"
                                                 mode="single"
                                                 :options="async function(query) {
                                                    return await selectChangeEvent(query)
                                                }"
                                                 id="category">
                                    </Multiselect>
                                    <InputError :message="errors.student_id"/>
                                </div>
                                <div class="col form-group">
                                    <label for="semester">Semester</label>
                                    <select id="semester" v-model="newResult.semester" class="form-control">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                    </select>
                                    <InputError :message="errors.semester"/>
                                </div>
                                <div class="col form-group">
                                    <label for="gpa">GPA</label>
                                    <input type="number" step="0.01" v-model="newResult.gpa" id="gpa" class="form-control">
                                    <InputError :message="errors.gpa"/>
                                </div>
                                <div class="col">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control" v-model="newResult.status">
                                        <option value="Passed">Passed</option>
                                        <option value="Referred">Referred</option>
                                        <option value="Dropout">Dropout</option>
                                    </select>
                                    <InputError :message="errors.status"/>
                                </div>
                                <div class="col">
                                    <label for="failed_in_subject">Failed in Subject</label>
                                    <input type="number" step="0.1" v-model="newResult.failed_in_subject" id="failed_in_subject" class="form-control">
                                    <InputError :message="errors.failed_in_subject"/>
                                </div>
                                <div class="col">
                                    <label for="supporting_document">Supporting Document</label>
                                    <input type="file" id="supporting_document" ref="supporting_document" accept="image/*" class="form-control" @input="newResult.supporting_document = $event.target.files" multiple="true">
                                    <InputError :message="errors.supporting_document"/>
                                </div>
                                <div class="col mt-4">
                                    <input type="submit" class="btn btn-success" value="Add">
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-secondary">
                        <thead>
                        <tr class="text-center">
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">SL#</th>
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Name</th>
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Institute</th>
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Dept.</th>
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Session</th>
                            <th colspan="6" style="vertical-align : middle;text-align:center;">Details Result</th>
                        </tr>
                        <tr>
                            <th style="vertical-align : middle;text-align:center;">Semester</th>
                            <th style="vertical-align : middle;text-align:center;">GPA</th>
                            <th style="vertical-align : middle;text-align:center;">Status</th>
                            <th style="vertical-align : middle;text-align:center;">Failed(if any)</th>
                            <th style="vertical-align : middle;text-align:center;">Attachments</th>
                            <th style="vertical-align : middle;text-align:center;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(result, index) in data.data" :key="index">
                            <td>
                                {{index + 1}}
                            </td>
                            <td>
                                <InertiaLink :href="route('madrasa.student.show', result.id)">
                                    {{result.name}}
                                </InertiaLink>
                            </td>
                            <td>
                                {{ result?.polytechnic?.name }}
                            </td>
                            <td>
                                {{ result?.polytechnic_trade_id }}
                            </td>
                            <td>
                                {{ result?.polytechnic_session }}
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li class="list-item" v-for="semester in result.results">
                                        {{ semester.semester }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li :class="gpa.gpa == null ? 'text-red-700 list-item' : 'list-item'" v-for="gpa in result.results">
                                        {{ gpa.gpa == null ? 'N' : gpa.gpa  }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li class="list-item" v-for="status in result.results">
                                        {{ status.status }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li class="list-item" v-for="failed_in_subject in result.results">
                                        {{ failed_in_subject.failed_in_subject }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li class="list-item" v-for="attachments in result.results">
                                        <vue-picture-swipe v-if="attachments.attachments.length !== 0" :items="imageItems(attachments.attachments)" />
                                        <p class="p-0 m-0 text-red-700" v-else>N</p>
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-group list-group-flush">
                                    <li class="list-item" v-for="failed_in_subject in result.results">
                                        <inertia-link
                                            method="DELETE"
                                            as="button"
                                            type="button"
                                            :href="route('polytechnic.result.destroy', failed_in_subject.id)"
                                            v-if="can.delete"
                                        >
                                            <!--                                        <jet-button type="submit" class="btn-sm btn-danger px-0 py-0">-->
                                            <font-awesome-icon
                                                icon="trash"
                                                size="xs"
                                                rotation="rotate"
                                                class="text-danger"
                                            ></font-awesome-icon>
                                            <!--                                        </jet-button>-->
                                        </inertia-link>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator :paginator="data"></Paginator>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Paginator from "@/Components/Paginator";
import InputError from "@/Components/InputError";
import Multiselect from '@vueform/multiselect'
import JetButton from "@/Shared/Button";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faInfo);
function GET() {
    var data = [];
    for(let x = 0; x < arguments.length; ++x){
        if(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)"))) !== null){
            data.push(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)")))[1])
        }
    }
    return data;
}
export default {
    name: "Index",
    props: ['sessions', 'data', 'errors', 'can'],
    components: {
        Multiselect,
        InputError,
        Paginator,
        CardHeader,
        Authenticated,
        JetButton
    },
    data() {
        return {
            filters: {
                academic_session: GET('academic_session')[0]
            },
            newResult: {
                status: "",
                gpa: '',
                semester: '',
                failed_in_subject: 0,
                student_id: "",
                supporting_document: []
            },
            options: []
        }
    },
    mounted() {
    },
    methods: {
        async filteredSessions(){
            this.$inertia.replace(route('polytechnic.result.index', this.filters))
        },
        search(params){
            this.$inertia.replace(route('polytechnic.result.index', {'search': params}))
        },
        addResult(){

        },
        async selectChangeEvent(query){
            let where = {}
            let session = GET('academic_session')
            if (query) {
                where = {'name': query}
            }
            if(session.length > 0){
                where['academic_session'] = session[0]
            }
            const response = await fetch(route('student_list', where))
            const data = await response.json();
            return data;
        },
        submitForm(){
            this.$inertia.post(route('polytechnic.result.store'), this.newResult)
        },
       imageItems(images){
            let items = []
            if (images !== null && images.length > 0){
                images.forEach(function (value, index){
                    var item = {
                        src: '/'+value.attachment,
                        thumbnail: '/'+value.attachment,
                        w: 1200,
                        h: 900,
                        title: 'Will be used for caption'
                    }
                    items.push(item)
                })
            }
            return items;
        }
    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

<style>
.pswp__img--placeholder--blank{
    display: none !important;
}
.gallery-thumbnail img {
    object-fit: contain !important;
    max-width: 1rem!important;
    margin: 0!important;
    margin-bottom: 0!important;
    margin-top: 0!important;
}
.gallery-thumbnail{
    display: flex!important;
    margin: 0 0.2rem 0.09rem 0!important;
}
.pswp__bg {
    background-color: #fff !important;
}
.Pswp_bg image-zoom-background {
    background-color: #fff !important;
}
.my-gallery{
    display: inline-flex!important;
}
</style>
