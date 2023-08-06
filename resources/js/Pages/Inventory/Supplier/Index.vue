<template>
    <Head>
        <title>Supplier Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Supplier Management</page-header>
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
                                <div class="flex flex-col md:flex-row justify-content-center md:space-x-8">
                                    <div class="space-x-2 mb-6">
                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="form.name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        >
                                    </div>
                                    <div class="space-x-2 mb-6">
                                        <label for="details" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                                        <Select2
                                            v-model="form.category_id"
                                            :options="categories"
                                            :settings="{
                                                dropdownAutoWidth: true,
                                                placeholder: 'Select a Category',
                                                width: '100%'
                                            }"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        />
                                    </div>
                                    <div class="space-x-2 mb-6">
                                        <label for="contact_person" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Person</label>
                                        <input
                                            type="text"
                                            id="contact_person"
                                            v-model="form.contact_person"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        >
                                    </div>
                                    <div class="space-x-2 mb-6">
                                        <label for="contact_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                                        <input
                                            type="tel"
                                            id="contact_number"
                                            v-model="form.contact_number"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        >
                                    </div>
                                    <div class="space-x-2 mb-6">
                                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                                        <input
                                            type="text"
                                            id="address"
                                            v-model="form.address"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        >
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
                                    <TableHead>Category</TableHead>
                                    <TableHead>Contact P.</TableHead>
                                    <TableHead>Contact N.</TableHead>
                                    <TableHead>Address</TableHead>
                                    <TableHead>Actions</TableHead>
                                </template>

                                <template #tbody-sn="{sn}">
                                    <TableHead v-text="sn.toString().padStart(2, '0')"/>
                                </template>

                                <template #tbody="{row}">
                                    <TableBody v-text="row.name" />
                                    <TableBody v-text="row.category?.name" />
                                    <TableBody v-text="row.contact_person" />
                                    <TableBody v-text="row.contact_number" />
                                    <TableBody v-text="row.address" />
                                    <TableBody>
                                        <InertiaLink
                                            :href="route('supplier.destroy', row.id)"
                                            method="DELETE"
                                            class="p-2 rounded backdrop-blur-2xl bg-[#36AFAD] no-underline text-black"
                                        >Delete</InertiaLink>
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
    name: 'InventorySupplier',
    props: ['can', 'data', 'categories'],
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
                category_id: '',
                contact_person: '',
                contact_number: '',
                address: '',
                __token: this.$page.props.csrf
            })
        }
    },
    methods: {
        search(){
        },
        formSubmit(){
            this.form.post(route('supplier.store'), {
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
.select2-container--default .select2-selection--single {
    background-color: #F9FAFB;
    border: 0!important;
    border-radius: 4px;
}
</style>
