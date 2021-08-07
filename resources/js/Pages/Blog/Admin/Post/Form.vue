<template>
    <div class="flex h-screen justify-center mt-5">
        <form action="" class="w-3/4" @submit.prevent="submit()">
            <div class="row w-100">
                <div class="col-md-8">
                    <div class="row g-3 align-items-center">
                        <div class="col-12">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" v-model="formData.title" required>
                            <div v-if="errors.title" class="text-danger">
                                {{ errors.title }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="slug" class="form-label">Url</label>
                            <input type="hidden" class="form-control" id="slug" v-model="formData.slug">
                            <a :href="slug" class="form-control"> {{slug}}</a>
                            <div v-if="errors.title" class="text-danger">
                                {{ errors.title }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="title" class="form-label">Content</label>
                            <ckeditor v-model="formData.content" :editor="ckeditor" :config="ckeditorConfig"></ckeditor>
                            <div v-if="errors.title" class="text-danger">
                                {{ errors.title }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row g-3">
                        <label for="category">Category</label>
                        <Multiselect v-model="formData.categories"
                                     :filterResults="true"
                                     :minChars="1"
                                     :resolveOnLoad="true"
                                     :delay="0"
                                     :searchable="true"
                                     mode="multiple"
                                     limit="50"
                                     :options="async function(query) {
                                                return await fetchCategories(query)
                                              }"
                                     id="category">
                        </Multiselect>
                        <span>{{formData.categories}}</span>
                    </div>
                    <div class="row g-3">
                        <label for="tags">Tags</label>
                        <Multiselect v-model="formData.tags"
                                     :filterResults="true"
                                     :minChars="1"
                                     :resolveOnLoad="true"
                                     :delay="0"
                                     :searchable="true"
                                     mode="tags"
                                     limit="50"
                                     :options="async function(query) {
                                                return await fetchCategories(query)
                                              }"
                                     id="tags">
                        </Multiselect>
                        <span>{{formData.tags}}</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import CKEditor from "@ckeditor/ckeditor5-vue"
import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
import Multiselect from '@vueform/multiselect'
export default {
    name: "Form",
    props: ['user', 'errors'],
    components: {
        ckeditor: CKEditor.component,
        Multiselect
    },
    data(){
        return {
            ckeditor: ClassicEditor,
            ckeditorConfig: {
                height: "600",
            },
            formData: {
                title: "",
                meta_title: "",
                slug: "",
                published_at: "",
                post_type: "",
                summary: "",
                content: "",
                thumbnail: "",
                author: this.user.id,
                post_status: "",
                categories: [],
                tags: []
            }
        }
    },
    methods: {
        submit(){

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
        },
    },
    computed: {
        slug(){
            const baseUrl = window.location.origin+"/post/";
            const title = this.formData.title.toLowerCase();
            const slugify = title.replace(" ", "_");
            this.formData.slug = baseUrl + slugify
            return baseUrl + slugify
        }
    }

}
</script>

<style>
.ck-editor__editable {
    min-height: 40vh;
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
