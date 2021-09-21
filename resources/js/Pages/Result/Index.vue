<template>
    <Head>
        <title>Result Management System</title>
    </Head>
    <Authenticated>
        <div class="card">
            <div class="card-header">
                <CardHeader :create="false">
                    <template #first>
                        <div class="form-group row">
                            <label for="session" class="col-3">Session</label>
                            <div class="col-8">
                                <select name="session" id="session" class="form-control" v-model="filters.academic_session" @change.prevent="filteredSessions">
                                    <option value=""></option>
                                    <option v-for="session in sessions" :value="session.session">{{session.session}}</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </CardHeader>
            </div>
            <div class="card-body">
                <table class="table table-secondary text-center">
                    <thead>
                    <tr class="text-center">
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">SL#</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Name</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Institute</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Dept.</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Session</th>
                        <th colspan="2" style="vertical-align : middle;text-align:center;">Details Result</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Status</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Failed(if any)</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;"></th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;"></th>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <th>Result</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(result, index) in data">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{ result.name }}
                        </td>
                        <td>
                            {{ result.polytechnic.name }}
                        </td>
                        <td>
                            {{ result.polytechnic_trade_id }}
                        </td>
                        <td>
                            {{ result.polytechnic_session }}
                        </td>
                        <td>
                            {{ result.results.semester }}
                        </td>
                        <td>
                            {{ result.results.gpa }}
                        </td>
                        <td>
                            {{ result.results.status }}
                        </td>
                        <td>
                            {{ result.results.failed_in_subject }}
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";

function GET() {
    var data = [];
    for(let x = 0; x < arguments.length; ++x)
        data.push(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)")))[1])
    return data;
}
export default {
    name: "Index",
    props: ['sessions', 'data'],
    components: {CardHeader, Authenticated},
    data() {
        return {
            filters: {
                academic_session: GET('academic_session')[0]
            },
        }
    },
    mounted() {

    },
    methods: {
        filteredSessions(){
            this.$inertia.replace(route('result.index', this.filters))
        }

    }
}
</script>

<style scoped>

</style>
