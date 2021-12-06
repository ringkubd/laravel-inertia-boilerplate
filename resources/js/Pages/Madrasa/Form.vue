<template>
    <div>
        <div class="card mt-1">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('madrasa.index')"></back>
                    </div>
                    <div class="card-body form-inline">
                        <form action="" v-on:load="onloadForm()" @submit.prevent="postData">
                            <div class="row mb-4">
                                <div class="col shadow-lg py-2">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" class="form-control" id="name" tabindex="1" v-model="formData.name">
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="col shadow-lg py-2">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" v-model="formData.email">
                                    <div v-if="errors.email" class="text-danger">
                                        {{ errors.email }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md shadow-lg py-2">
                                    <label for="telephone">Telephone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" v-model="formData.telephone">
                                    <div v-if="errors.telephone" class="text-danger">
                                        {{ errors.telephone }}
                                    </div>
                                </div>
                                <div class="col shadow-lg py-2">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" v-model="formData.mobile">
                                    <div v-if="errors.mobile" class="text-danger">
                                        {{ errors.mobile }}
                                    </div>
                                </div>

                                <div class="col shadow-lg py-2">
                                    <label for="fax">Fax</label>
                                    <input type="tel" class="form-control" id="fax" name="fax" v-model="formData.fax">
                                    <div v-if="errors.fax" class="text-danger">
                                        {{ errors.fax }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group shadow-lg py-2">
                                    <label for="district">District</label>
                                    <select class="form-control" name="district" id="district" v-model="formData.district">
                                        <option value=""></option>
                                        <option :key="d.id" v-for="(d) in districts" :value="d.name">{{d.name}}</option>
                                    </select>
                                    <div v-if="errors.district" class="text-danger">
                                        {{ errors.district }}
                                    </div>
                                </div>

                                <div class="form-group shadow-lg py-2">
                                    <label for="address">Address</label>
                                    <textarea type="text" class="form-control" id="address" name="address" v-model="formData.address"></textarea>
                                    <div v-if="errors.address" class="text-danger">
                                        {{ errors.address }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-3 flex justify-end shadow-lg">
                                <jet-button v-if="createForm" type="submit">Add</jet-button>
                                <jet-button v-if="!createForm" type="submit">Update</jet-button>
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
export default {
    name: "form",
    components: {
        Back,
        JetButton
    },
    props: ['dbValue', 'flash', 'createForm','submitForm'],
    data(){
        return {
            formData: {
                name: this.dbValue.name,
                email: this.dbValue.email,
                address: this.dbValue.address,
                telephone: this.dbValue.telephone,
                mobile: this.dbValue.mobile,
                fax: this.dbValue.fax,
                district:this.dbValue.district,
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
        console.log(this.dbValue)
    },
    updated() {
    },
    beforeMount() {

    }
}
</script>

<style scoped>

</style>
