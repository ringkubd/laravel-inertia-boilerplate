<template>
    <div>
        <div class="card">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('polytechnic.index')"></back>
                    </div>
                    <div class="card-body form-inline">
                        <form action="" v-on:load="onloadForm()" @submit.prevent="postData">
                            <div class="row mb-4">
                                <div class="col">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" class="form-control" id="name" tabindex="1" v-model="formData.name">
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" v-model="formData.email">
                                    <div v-if="errors.email" class="text-danger">
                                        {{ errors.email }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact_number" name="telephone" v-model="formData.contact_number">
                                    <div v-if="errors.contact_number" class="text-danger">
                                        {{ errors.contact_number }}
                                    </div>
                                </div>
                                <div class="col form-group">
                                    <label for="institution_number">Institute Number</label>
                                    <input type="number" class="form-control" id="institution_number" v-model="formData.institution_number">
                                    <div v-if="errors.institution_number" class="text-danger">
                                        {{ errors.institution_number }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea type="text" class="form-control" id="address" name="address" v-model="formData.address"></textarea>
                                    <div v-if="errors.address" class="text-danger">
                                        {{ errors.address }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col form-group flex justify-end mt-2">
                                    <input
                                        v-if="!createForm"
                                        type="submit"
                                        class="btn btn-block btn-success"
                                        value="Update"
                                    />
                                    <input
                                        v-else
                                        type="submit"
                                        class="btn btn-block btn-success"
                                        value="Create"
                                    />
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Back from "@/Shared/Back";
let districts = require('../../Data/districts.js')
import JetButton from "@/Components/Button"
import CardHeader from "@/Shared/CardHeader";
export default {
    name: "form",
    components: {
        CardHeader,
        Back,
        JetButton
    },
    props: ['dbValue', 'flash', 'createForm','submitForm', 'can'],
    data(){
        return {
            formData: {
                name: this.dbValue.name,
                email: this.dbValue.email,
                address: this.dbValue.address,
                contact_number: this.dbValue.contact_number,
                institution_number: this.dbValue.institution_number,
                __token: this.$page.props.csrf

            },
            errors: this.$page.props.errors,
            districts: districts,
            createForm: this.createForm
        }
    },
    methods: {
        onloadForm(){

        },
        postData(){
            const er = this.submitForm(this.formData)
        }
    },
    mounted() {

    },
    updated() {
    },
    beforeMount() {

    }
}
</script>

<style scoped>

</style>
