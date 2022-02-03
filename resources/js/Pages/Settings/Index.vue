<template>
    <Head>
        <title>App Settings</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                Setting Page
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card min-h-screen">
                    <div class="card-header">
                        <CardHeader :create="route('config.create')" :can="can"></CardHeader>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-auto">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>File Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(config, index) in configs">
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        {{ config }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo } from "@fortawesome/free-solid-svg-icons";
import CardHeader from "@/Shared/CardHeader";
library.add(faPen, faTrash, faInfo);
export default {
    name: "Index",
    props: ['configs', 'can'],
    components: {
        CardHeader,
        PageHeader,
        Authenticated
    },
    mounted() {
        console.log(this.$refs)
    },
    methods: {
        submit(e){
            console.log(e)
        }
    },
    computed: {
        configs(){
            return this.configs.filter((config) => {
                return config.includes('.php')
            })
        }
    }
}
</script>

<style scoped>

</style>
