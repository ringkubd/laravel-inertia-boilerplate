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
                    <div class="card-header"></div>
                    <div class="card-body table-responsive" v-html="configs"></div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "Index",
    props: ['configs'],
    components: {
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
    }
}
</script>

<style scoped>

</style>
