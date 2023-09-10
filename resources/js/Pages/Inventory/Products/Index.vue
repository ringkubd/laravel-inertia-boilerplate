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
                            <InertiaLink :href="route('product.create')">
                                <Button>Create</Button>
                            </InertiaLink>
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
                                        <div class="flex flex-row space-x-0.5">
                                            <InertiaLink :href="route('product.edit', row.id)">
                                                <Button class="backdrop-blur bg-amber-600">Edit</Button>
                                            </InertiaLink>
                                            <InertiaLink :href="route('product.destroy', row.id)" method="DELETE">
                                                <Button class="backdrop-blur bg-red-600">Delete</Button>
                                            </InertiaLink>
                                            <InertiaLink :href="route('product.disable', row.id)" method="PUT">
                                                <Button :class="row.status ? 'bg-amber-800' : 'bg-blue-600'">{{row.status ? 'Disable': 'Active'}}</Button>
                                            </InertiaLink>
                                        </div>

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
    import "@jobinsjp/vue3-datatable/dist/style.css"
    import Paginator from "@/Components/Paginator.vue";
    import Input from "@/Components/Input.vue";
    import Button from "@/Shared/Button.vue";

    export default {
        name: 'ProductIndex',
        components: {
            Button,
            Input,
            TableBodyCell, TableHead, Paginator, DataTable, TableBody,
            Back,
            Authenticated,
            PageHeader,
            CardHeader
        },
        props: ['can', 'data'],
        data(){
            return {
                form: {}
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
