<template>
    <div class="flex h-screen justify-center">
        <form class="w-3/4" @submit.prevent="submit">
            <div class="row g-3 align-items-center">
                <div class="col-md-12 form-group">
                    <fieldset class="fieldset-border">
                        <legend class="fieldset-border">Trade Name <span class="text-danger">*</span></legend>
                        <input type="text" id="name" required class="form-control" v-model="formData.name">
                    </fieldset>
                    <div v-if="errors.name" class="text-danger">
                        {{ errors.name }}
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <fieldset class="fieldset-border">
                        <legend class="fieldset-border">Institute <span class="text-danger">*</span></legend>
                        <div class="form-check">
                            <input type="radio" name="is_madrasa" id="is_madrasa" class="form-check-input" v-model="formData.is_madrasa" value="1">
                            <label class="form-check-label" for="is_madrasa">Madrasa</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="is_polytechnic" id="is_polytechnic" class="form-check-input" v-model="formData.is_madrasa" value="0">
                            <label class="form-check-label" for="is_polytechnic">Polytechnic</label>
                        </div>
                    </fieldset>
                    <div v-if="errors.is_madrasa" class="text-danger">
                        {{ errors.is_madrasa }}
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-3 align-items-center">
                <div class="w-1/3 flex justify-content-end">
                    <Button type="submit" v-if="formType == 'store'">Submit</Button>
                    <Button type="submit" v-if="formType == 'update'">Update</Button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import Multiselect from '@vueform/multiselect'
import Button from "@/Components/Button";
export default {
    name: "Form",
    props: ['trade', 'errors', 'form-type', 'submitForm'],
    components: {
        Button,
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
fieldset.fieldset-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
    box-shadow:  0px 0px 0px 0px #000;
}

legend.fieldset-border {
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
    width:inherit; /* Or auto */
    padding:0 10px; /* To give a bit of padding on the left and right */
    border-bottom:none;
}
legend {
    float: none;
    width: 100%;
    padding: 0 10px;
    margin-bottom: 0.5rem;
    font-size: calc(1.275rem + 0.3vw);
    line-height: inherit;
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
