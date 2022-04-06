<template>
    <div class="flex justify-center h-screen">
        <form action="" enctype="multipart/form-data" @submit.prevent="submitData">
            <fieldset class="row form-group mb-5 mx-2 pb-5 shadow-2xl">
                <legend class="hover:text-green-900 bg-gradient-to-l bg-gradient-to-r from-gray-300 to-blue-100 shadow-lg border-b-2 border-gray-500">Upload Application Details</legend>
                <div class="col">
                    <div class="flex sm:grid-cols-1 grid md:grid-cols-2 flex-grow mb-2">
                        <div class="flex-1 group mr-2">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" v-model="form.name" required class="form-control">
                            <div v-if="errors.name" class="text-danger">
                                {{ errors.name }}
                            </div>
                        </div>
                        <div class="flex-1 mr-2">
                            <label for="platform">Platform<span class="text-danger">*</span></label>
                            <select name="platform" id="platform" class="form-control" v-model="form.platform">
                                <option value="android">Android</option>
                                <option value="ios">IOS</option>
                            </select>
                            <div v-if="errors.platform" class="text-danger">
                                {{ errors.platform }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md">
                        <label for="version_code">Version<span class="text-danger">*</span></label>
                        <input type="text"  id="version_code" required v-model="form.version_code" class="form-control">
                        <div v-if="errors.version_code" class="text-danger">
                            {{ errors.version_code }}
                        </div>
                    </div>
                    <div class="col-md">
                        <label for="file_path">File<span class="text-danger">*</span></label>
                        <input type="file" id="file_path" class="form-control" ref="file_path" @input="form.file_path = $event.target.files[0]">
                        <div v-if="errors.file_path" class="text-danger">
                            {{ errors.file_path }}
                        </div>
                    </div>
                    <div class="col-md">
                        <label for="status">Status<span class="text-danger">*</span></label>
                        <select name="status" id="status" v-model="form.status" class="form-control">
                            <option value="1">Active</option>
                            <option value="2">Pending</option>
                            <option value="0">Disable</option>
                        </select>
                        <div v-if="errors.status" class="text-danger">
                            {{ errors.status }}
                        </div>
                    </div>
                </div>


            </fieldset>
            <div class="row form-group">
                <div class="col-4 justify-content-center align-items-end">
                    <input type="submit" class="btn btn-success rounded" v-if="createForm" value="Upload">
                    <input type="submit" class="btn btn-warning rounded" v-else value="Update">
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Form",
    props: ['errors', 'submitForm', 'createForm', 'application'],
    data() {
        return {
            form: useForm({
                name: this.application?.name,
                platform: this.application?.platform,
                file_path: this.application?.file_path,
                version_code: this.application?.version_code,
                status: this.application?.status
            }),
        }
    },
    methods:{
        submitData(){
            this.submitForm(this.form)
        }
    }
}
</script>

<style scoped>

</style>
