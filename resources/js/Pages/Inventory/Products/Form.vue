<template>
    <form action="" @submit.prevent="storeProduct">
        <div class="flex flex-col">
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="flex flex-col md:flex-row md:w-2/3 mb-4">
                    <label
                        for="title"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Title<sup>*</sup></label>
                    <div class="flex flex-col w-full p-2.5">
                        <input
                            type="text"
                            id="name"
                            required
                            v-model="form.title"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                        <span v-if="form.errors.title" class="text-red-600">{{form.errors.title}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
                        <input
                            type="text"
                            id="sl_no"
                            required
                            v-model="form.sl_no"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                        <span v-if="form.errors.sl_no" class="text-red-600">{{form.errors.sl_no}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
                        <Select2
                            v-model="form.unit"
                            :options="unit.map((u) => {
                                return {id: u.text, text: u.text}
                            })"
                            required
                            :settings="{
                            dropdownAutoWidth: true,
                            placeholder: 'Select a Category',
                            width: '100%',
                            tags: true
                        }"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <span v-if="form.errors.unit" class="text-red-600">{{form.errors.unit}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
                        <input
                            type="text"
                            id="description"
                            v-model="form.description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                        <span v-if="form.errors.description" class="text-red-600">{{form.errors.description}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
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
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <span v-if="form.errors.category_id" class="text-red-600">{{form.errors.category_id}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
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
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        />
                        <span v-if="form.errors.brand_id" class="text-red-600">{{form.errors.brand_id}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
                        <input
                            type="number"
                            id="class_room_id"
                            v-model="form.class_room_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                        <span v-if="form.errors.class_room_id" class="text-red-600">{{form.errors.class_room_id}}</span>
                    </div>
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
                    <div class="flex flex-col w-full p-2.5">
                        <input
                            type="text"
                            id="lesson"
                            v-model="form.lesson"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                        <span v-if="form.errors.lesson" class="text-red-600">{{form.errors.lesson}}</span>
                    </div>
                </div>
                <div>
                    Lesson
                </div>
            </div>
            <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                <div class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"></div>
                <input type="submit" value="Submit" class="p-2 bg-[#36AFAD] rounded" @click.prevent="storeProduct">
            </div>
            <!-- Product Meta -->
            <div v-if="product.id">
                <h2 class="block underline">Product Meta</h2>
                <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4 bg-[#002147] bg-opacity-10 p-2 text-center align-middle" v-for="m in product.meta">
                    <div class="flex flex-col md:flex-row md:w-2/3 md:space-x-2">
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            {{m.key}}
                        </div>
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            {{m.content}}
                        </div>

                    </div>
                    <div>
                        <button type="button" class="p-2 bg-[#36AFAD] rounded" @click="() => deleteMeta(m.id)">Delete</button>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row  min-w-full md:space-x-8 mb:mb-0 mb-4">
                    <div class="flex flex-col md:flex-row md:w-2/3 md:space-x-2">
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            <label
                                for="key"
                                class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Key
                            </label>
                            <div class="flex flex-col w-full p-2.5">
                                <input
                                    type="text"
                                    v-model="metaForm.key"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                >
                                <span v-if="metaForm.errors.key" class="text-red-600">{{metaForm.errors.key}}</span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:w-1/2">
                            <label
                                for="value"
                                class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Value
                            </label>
                            <div class="flex flex-col w-full p-2.5">
                                <input
                                    type="text"
                                    id="value"
                                    v-model="metaForm.content"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                >
                                <span v-if="metaForm.errors.content" class="text-red-600">{{metaForm.errors.content}}</span>
                            </div>
                        </div>

                    </div>
                    <div>
                        <button type="button" class="p-2 bg-[#36AFAD] rounded" @click="storeMeta">Add</button>
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
    props: ['product', 'unit', 'category', 'brand', 'edit'],
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
                content: ''
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
        storeProduct(){
            if (this.edit){
                this.form.put(route('product.update', this.product.id))
            }else{
                this.form.post(route('product.store'))
            }
        },
        storeMeta(){
            this.metaForm.put(route('product.add_meta', this.product.id), {
                preserveState: true,
                preserveScroll: true
            })
        },
        deleteMeta(metaId){

        }
    }
}
</script>

<style>
.select2-container--default .select2-selection--single {
    background-color: #F9FAFB;
    border: 0!important;
}
</style>
