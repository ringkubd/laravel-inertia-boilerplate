<template>
    <div class="flex h-screen justify-center mt-1">
        <form class="w-3/4" @submit.prevent="submit">
            <div class="row g-3 align-items-center">
                <div class="col-md-6 form-group">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" id="title" required class="form-control" v-model="formData.title">
                    <div v-if="errors.title" class="text-danger">
                        {{ errors.title }}
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="meta_title" class="form-label">Meta Title <span class="text-danger">*</span></label>
                    <input type="text" id="meta_title" class="form-control" v-model="metaTitle">
                    <div v-if="errors.meta_title" class="text-danger">
                        {{ errors.meta_title }}
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-3 align-items-center">
                <div class="col-12 form-group">
                    <label for="parent_id">Parent</label>
                    <Multiselect v-model="formData.parent_id"
                                 :filterResults="true"
                                 :minChars="1"
                                 :delay="0"
                                 :searchable="true"
                                 mode="single"
                                 limit="50"
                                 :options="async function(query) {
                                                return await fetchCategories(query)
                                              }"
                                 id="parent">
                    </Multiselect>
                    <div v-if="errors.categories" class="text-danger">
                        {{ errors.categories }}
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-3 align-items-center">
                <div class="col-12 form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" cols="30" rows="5" v-model="formData.description"></textarea>
                    <div v-if="errors.description" class="text-danger">
                        {{ errors.description }}
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
    props: ['formContent', 'parent', 'errors', 'form-type', 'submitForm'],
    components: {
        Multiselect
    },
    data () {
        return {
            formData: {
                title: this.formContent.title,
                meta_title: this.formContent.meta_title,
                description: this.formContent.description,
                parent_id: this.parent,
            }
        }
    },
    methods: {
        submit() {
            this.submitForm(this.formData)
        },
        async fetchCategories(query){
            let where = ''

            if (query) {
                where = {'name': query}
            }
            const response = await fetch(route('category.get', where))
            const data = await response.json();
            const finalData = data.data.map((item) => {
                return { value: item.id, label: item.title }
            })
            return finalData;
        }
    },
    computed: {
        metaTitle(){
            return this.formData.meta_title = this.formData.title
        }
    },
}
</script>

<style scoped>

</style>
<style src="@vueform/multiselect/themes/default.css"></style>
