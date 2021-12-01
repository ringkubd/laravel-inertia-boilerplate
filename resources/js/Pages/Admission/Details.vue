<template>
    <Head>
        <title>Admission Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Admission Management</page-header>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card flex min-h-screen justify-center mt-5">
                    <div class="card-header">
                        <Back :back-url="route('admission.index')"></Back>
                    </div>
                    <div class="card-body col-8 offset-2">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td colspan="2">
                                    <img class="img-thumbnail w-25" :src="'/'+admission?.student?.photo" alt="">
                                </td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{admission?.student?.name}}</td>
                            </tr>
                            <tr>
                                <th>Madrasah</th>
                                <td>{{admission?.student?.madrasha?.name}}</td>
                            </tr>
                            <tr>
                                <th>Session</th>
                                <td>{{ admission?.academic_session }}</td>
                            </tr>
                            <tr>
                                <th>Tracking ID</th>
                                <td>{{ admission?.tracking_id }}</td>
                            </tr>
                            <tr>
                                <th>Admission Fee</th>
                                <td>{{ admission?.admission_fee }}</td>
                            </tr>
                            <tr>
                                <th>Money Receipt</th>
                                <td>{{ admission?.money_receipt }}</td>
                            </tr>
                            <tr>
                                <th>Polytechnic Choice</th>
                                <td>
                                    <ul class="list-group list-group-numbered">
                                        <li v-if="admission?.polytechnic" v-for="poly in admission?.polytechnic">{{poly.name}}</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Trade Choice</th>
                                <td>
                                    <ol class="list-group list-group-numbered ml-0 pl-0">
                                        <li class="ml-0 pl-0" v-if="admission?.trade" v-for="tr in admission?.trade">{{tr.name}}</li>
                                    </ol>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{admission.status}}</td>
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
import Back from "@/Shared/Back";
export default {
    name: "Details",
    props:['admission'],
    components: {Back, Authenticated}
}
</script>

<style scoped>

</style>
