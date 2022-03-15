<template>
    <Head>
        <title>BTEB Result Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">BTEB Result Management</h2>
            </PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader>
                        <template #first>
                            <NavLink :href="route('bteb.new')" class="bg-green-600 rounded">Create</NavLink>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>SL#</th>
                            <th>Name</th>
                            <th>E. Year</th>
                            <th>Subject</th>
                            <th>Result</th>
                            <th>CGPA</th>
                            <th>Roll</th>
                            <th>Reg.</th>
                            <th>Session</th>
                            <th>Father's Name</th>
                            <th>Mother's Name</th>
                            <th>Institute</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(result, index) in results.data">
                            <td>{{index}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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
import NavLink from "@/Components/NavLink";
export default {
    name: "Index",
    components: {NavLink, CardHeader, PageHeader, Authenticated},
    props: ['results']
}
</script>

<style scoped>

</style>
