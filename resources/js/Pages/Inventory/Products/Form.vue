<template>
    <form action="">
        <div class="flex flex-col">
            <div class="flex flex-row  min-w-full  space-x-8">
                <div class="flex flex-row md:w-2/3 mb-4">
                    <label
                        for="title"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Title</label>
                    <input
                        type="text"
                        id="name"
                        v-model="form.title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Product title
                </div>
            </div>
            <div class="flex flex-row  min-w-full mb-4  space-x-8">
                <div class="flex flex-row md:w-2/3">
                    <label
                        for="sl_no"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Serial No</label>
                    <input
                        type="text"
                        id="sl_no"
                        v-model="form.sl_no"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                </div>
                <div>
                    Product serial no
                </div>
            </div>
            <div class="flex flex-row  min-w-full mb-4 space-x-8">
                <div class="flex flex-row md:w-2/3">
                    <label
                        for="sl_no"
                        class="md:w-1/3 block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Unit</label>
                    <Select2
                        v-model="form.unit"
                        :options="unit"
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
        </div>
    </form>
</template>
<script>
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: 'productsForm',
    props: ['product', 'unit'],
    components: {
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
            })
        }
    },
    methods: {
    }
}
</script>

<style>
.select2-container--default .select2-selection--single {
    background-color: #F9FAFB;
    border: 0!important;
}
</style>
