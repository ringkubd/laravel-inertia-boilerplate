<template>
    <div class="flex h-screen justify-center mt-5">
        <form class="w-3/4" @submit.prevent="submit">
            <div class="row g-3 align-items-center">
                <div class="col-md-6 form-group">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" required class="form-control" v-model="formData.name">
                    <div v-if="errors.name" class="text-danger">
                        {{ errors.name }}
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="meta_title" class="form-label">Type <span class="text-danger">*</span></label>
                    <input type="text" id="meta_title" class="form-control" v-model="formData.is_madrasa">
                    <div v-if="errors.is_madrasa" class="text-danger">
                        {{ errors.is_madrasa }}
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-3 align-items-center">
                <div class="w-1/3">
                    <input type="submit"  v-if="formType == 'store'" class="btn btn-primary pull-right">
                    <input type="submit" v-if="formType == 'update'" value="Update" class="btn btn-info pull-right">
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import Multiselect from '@vueform/multiselect'
export default {
    name: "Form",
    props: ['trade', 'errors', 'form-type', 'submitForm'],
    components: {
        Multiselect
    },
    data () {
        return {
            formData: {
                name: this.trade.name,
                is_madrasa: this.trade.is_madrasa,
            }
        }
    },
    methods: {
        submit() {
            this.submitForm(this.formData)
        }
    },
    computed: {

    },
}
</script>

<style scoped>

</style>
<style src="@vueform/multiselect/themes/default.css"></style>
