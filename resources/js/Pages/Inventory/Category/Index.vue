<template>
    <Head>
        <title>Category Brand</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Category Management</page-header>
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
                        <!-- Form  -->
                        <div class="form-inline">
                            <form @submit.prevent="formSubmit">
                                <div class="flex flex-row justify-content-center space-x-8">
                                    <div class="space-x-2 mb-6">
                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                        <input type="text" id="name" v-model="form.name"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div class="space-x-2 mb-6">
                                        <label for="details" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Details</label>
                                        <input type="text" id="details" v-model="form.details" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div class="space-x-2 mt-6">
                                        <input type="submit" class="p-2 bg-[#36AFAD] rounded" value="Submit" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- End Form  -->
                        <!-- Table  -->
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
                                    <TableHead>Name</TableHead>
                                    <TableHead>Details</TableHead>
                                    <TableHead>Actions</TableHead>
                                </template>

                                <template #tbody-sn="{sn}">
                                    <TableHead v-text="sn.toString().padStart(2, '0')"/>
                                </template>

                                <template #tbody="{row}">
                                    <TableBody v-text="row.name" />
                                    <TableBody class="max-w-md" v-text="row.details" />
                                    <TableBody>
                                        <InertiaLink :href="route('category.destroy', row.id)" method="DELETE">Delete</InertiaLink>
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
                        <!-- Table End  -->
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
<script>

import Authenticated from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Shared/PageHeader.vue";
import Back from "@/Shared/Back.vue";
import CardHeader from "@/Shared/CardHeader.vue";
import Button from "@/Shared/Button.vue";
import Input from "@/Components/Input.vue";

import { DataTable, TableBodyCell, TableHead, TableBody } from "@jobinsjp/vue3-datatable"
import "@jobinsjp/vue3-datatable/dist/style.css"
import {useForm} from "@inertiajs/inertia-vue3";
import Paginator from "@/Components/Paginator.vue";

export default {
    name: 'InventoryCategory',
    props: ['can', 'data'],
    components: {
        Paginator,
        Input,
        Button,
        CardHeader,
        Back,
        PageHeader,
        Authenticated,
        DataTable,
        TableBodyCell,
        TableHead,
        TableBody
    },
    data(){
        return {
            form: useForm({
                name: '',
                details: '',
                __token: this.$page.props.csrf
            })
        }
    },
    methods: {
        search(){
        },
        formSubmit(){
            this.form.post(route('category.store'), {
                onSuccess: (s) => {
                    this.form.reset()
                },
                onError: (e) => console.log(e)
            })
        }
    }
}
</script>

<style>
td{
    white-space: -o-pre-wrap!important;
    word-wrap: break-word!important;
    white-space: pre-wrap!important;
    white-space: -moz-pre-wrap!important;
    white-space: -pre-wrap!important;
    min-width: fit-content!important;
    text-align: justify!important;
}
</style>
