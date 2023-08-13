<template>
    <form action="">
        <div class="flex flex-col">
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3 mb-4">
                    <label
                        for="title"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Title<sup>*</sup></label>
                    <input
                        type="text"
                        id="name"
                        required
                        v-model="form.title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Product title
                </div>
            </div>
            <!--Serial No -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="sl_no"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Serial No<sup>*</sup></label>
                    <input
                        type="text"
                        id="sl_no"
                        required
                        v-model="form.sl_no"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Product serial no
                </div>
            </div>
            <!--Unit -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="sl_no"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Unit<sup>*</sup></label>
                    <Select2
                        v-model="form.unit"
                        :options="unit"
                        required
                        :settings="{
                            dropdownAutoWidth: true,
                            placeholder: 'Select a Category',
                            width: '100%',
                            tags: true
                        }"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>
                <div>
                    Product unit.
                    <p>Like kg, feet, meter</p>
                </div>
            </div>
            <!--Description -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="description"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Description</label>
                    <input
                        type="text"
                        id="description"
                        v-model="form.description"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Product description
                </div>
            </div>
            <!--Category -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="category_id"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Category<sup>*</sup></label>
                    <Select2
                        v-model="form.category_id"
                        :options="category"
                        @select="storeNewCategory"
                        :settings="{
                            dropdownAutoWidth: true,
                            placeholder: 'Select a Category',
                            width: '100%',
                             tags: true,
                            createTag: createNewOption
                        }"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>
                <div>
                    Product description
                </div>
            </div>
            <!--Brand No -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="brand_id"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Brand</label>
                    <Select2
                        v-model="form.brand_id"
                        :options="brand"
                        @select="storeNewBrand"
                        :settings="{
                            dropdownAutoWidth: true,
                            placeholder: 'Select a Brand',
                            width: '100%',
                            tags: true,
                            createTag: createNewOption
                        }"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>
                <div>
                    Product Brand
                </div>
            </div>
            <!-- Class -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="class_room_id"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Class</label>
                    <input
                        type="number"
                        id="class_room_id"
                        v-model="form.class_room_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Class
                </div>
            </div>
            <!-- Lesson -->
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3">
                    <label
                        for="lesson"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Lesson</label>
                    <input
                        type="text"
                        id="lesson"
                        v-model="form.lesson"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Lesson
                </div>
            </div>
            <!-- Product Meta -->
            <div>
                <h2 class="block underline">Product Meta</h2>
                <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                    <div class="flex flex-col md:flex-row md:w-2/3 md:space-x-2">
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            <label
                                for="key"
                                class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Key
                            </label>
                            <input
                                type="text"
                                v-model="metaForm.key"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                        </div>
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            <label
                                for="value"
                                class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Value
                            </label>
                            <input
                                type="text"
                                id="value"
                                v-model="metaForm.value"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                        </div>

                    </div>
                    <div>
                        <button type="button" class="p-2 bg-[#36AFAD] rounded">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script>
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/inertia-vue3";
import { Inertia } from '@inertiajs/inertia'
import Button from "@/Shared/Button.vue";

export default {
    name: 'productsForm',
    props: ['product', 'unit', 'category', 'brand'],
    components: {
        Button,
        Input,
    },
    data(){
        return {
            form: useForm({
                title: this.product.title,
                sl_no: this.product.sl_no,
                unit: this.product.unit,
                description: this.product.description,
                category_id: this.product.category_id,
                brand_id: this.product.brand_id,
                class_room_id: this.product.class_room_id,
                lesson: this.product.lesson,
            }),
            metaForm: useForm({
                key: '',
                value: ''
            })
        }
    },
    methods: {
        createNewOption(params){
            const term = params.term.trim();
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // add additional parameters
            }
        },
        storeNewBrand(params){
            if (params.newTag){
                Inertia.post(route('brand.store'), {
                    name: params.text
                })
            }
        },
        storeNewCategory(params){
            if (params.newTag){
                Inertia.post(route('category.store'), {
                    name: params.text
                })
            }
        },
    }
}
</script>

<style>
.select2-container--default .select2-selection--single {
    background-color: #F9FAFB;
    border: 0!important;
}
</style>
