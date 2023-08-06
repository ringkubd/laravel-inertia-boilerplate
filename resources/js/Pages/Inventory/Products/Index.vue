<template>
    <Head>
        <title>Product Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Product Management</page-header>
        </template>
        <div class="container-fluid min-h-screen">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :search-method="search">
                        <template #first>
                            <Back :back-url="route('inventory.index')"></Back>
                        </template>
                    </CardHeader>
                </div>
                <div class="card-body">
                    <div class="flex flex-column">
                        <div>
                            <DataTable
                                :rows="data.data"
                                sn
                                filter
                            >
                                <template #thead-sn>
                                    <TableHead>SN</TableHead>
                                </template>
                                <template #thead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Serial</TableHead>
                                    <TableHead>Unit</TableHead>
                                    <TableHead>Description</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Brand</TableHead>
                                    <TableHead>Class</TableHead>
                                    <TableHead>Lesson</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </template>

                                <template #tbody-sn="{sn}">
                                    <TableHead v-text="sn.toString().padStart(2, '0')"/>
                                </template>

                                <template #tbody="{row}">
                                    <TableBody v-text="row.title" />
                                    <TableBody v-text="row.sl_no" />
                                    <TableBody v-text="row.unit" />
                                    <TableBody v-text="row.description" />
                                    <TableBody v-text="row.category?.name" />
                                    <TableBody v-text="row.brand?.name" />
                                    <TableBody v-text="row.class?.name" />
                                    <TableBody v-text="row.lesson" />
                                    <TableBody v-text="row.status ? 'Active' : 'Inactive'" />
                                    <TableBody>
                                        <InertiaLink :href="route('product.destroy', row.id)" method="DELETE">Delete</InertiaLink>
                                    </TableBody>
                                </template>

                                <template #empty>
                                    <TableBodyCell colspan="2">
                                        No record found.
                                    </TableBodyCell>
                                </template>
                            </DataTable>
                            <Paginator :paginator="data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
<script>
    import Authenticated from "@/Layouts/Authenticated.vue";
    import PageHeader from "@/Shared/PageHeader.vue";
    import CardHeader from "@/Shared/CardHeader.vue";
    import Back from "@/Shared/Back.vue";
    import {DataTable, TableBody, TableBodyCell, TableHead} from "@jobinsjp/vue3-datatable";
    import Paginator from "@/Components/Paginator.vue";

    export default {
        name: 'ProductIndex',
        components: {
            TableBodyCell, TableHead, Paginator, DataTable, TableBody,
            Back,
            Authenticated,
            PageHeader,
            CardHeader
        },
        props: ['can', 'data'],
        data(){
            return {

            }
        },
        methods: {
            search(){
            },
        }
    }
</script>

<style scoped>

</style>
