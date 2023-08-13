<template>
    <div class="flex h-screen justify-center mt-5">
        <form action="" class="w-3/4" @submit.prevent="update">
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
                            <label for="content" class="form-label">Content</label>
                            <ckeditor id="content" v-model="formData.content" :editor="ckeditor" :config="ckeditorConfig"></ckeditor>
                            <input type="hidden" class="form-control" v-model="summary" id="" cols="30" rows="10"/>
                            <div v-if="errors.content" class="text-danger">
                                {{ errors.content }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row g-3">
                        <label class="form-label" for="category">Category</label>
                        <Multiselect v-model="formData.categories"
                                     :filterResults="true"
                                     :minChars="1"
                                     :delay="0"
                                     :searchable="true"
                                     mode="multiple"
                                     limit="50"
                                     :options="async function(query) {
                                                return await fetchCategories(query)
                                              }"
                                     id="category">
                        </Multiselect>
                        <div v-if="errors.categories" class="text-danger">
                            {{ errors.categories }}
                        </div>
                    </div>
                    <div class="row g-3 pt-3">
                        <label class="form-label" for="tags">Tags</label>
                        <Multiselect v-model="formData.tags"
                                     :filterResults="true"
                                     :minChars="1"
                                     :delay="0"
                                     :searchable="true"
                                     mode="tags"
                                     limit="50"
                                     :createTag="true"
                                     :appendNewTag="false"
                                     @tag="createTag"
                                     :options="async function(query) {
                                                return await fetchTags(query)
                                              }"
                                     id="tags">
                        </Multiselect>
                        <div v-if="errors.tags" class="text-danger">
                            {{ errors.tags }}
                        </div>
                    </div>
                    <div class="row g-3 pt-5">
                        <fieldset class="fieldset-border">
                            <legend class="fieldset-border">Status</legend>
                            <div class="form-check">
                                <input type="radio" name="post_status" id="post_status" class="form-check-input" v-model="formData.post_status" value="active">
                                <label class="form-check-label" for="post_status">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="post_status_draft" id="post_status_draft" class="form-check-input" v-model="formData.post_status" value="draft">
                                <label class="form-check-label" for="post_status_draft">Draft</label>
                            </div>
                        </fieldset>
                        <div v-if="errors.post_status" class="text-danger">
                            {{ errors.post_status }}
                        </div>
                    </div>
                    <div class="row g-3 pt-3">
                        <label class="form-label" for="thumbnail">Thumbnail</label>
                        <input type="file" @change="uploadImage($event)" name="thumbnail" id="thumbnail"  accept="image/*" class="form-control">
                        <div v-if="errors.thumbnail" class="text-danger">
                            {{ errors.thumbnail }}
                        </div>
                        <img :src="formData.thumbnail" :alt="formData.title" max-width="10" max-height="10" v-if="formData.thumbnail != null" class="rounded img-thumbnail">
                    </div>
                </div>
            </div>
            <div class="row mt-md-5 mt-2">
                <div class="col-md-4">
                    <input type="submit" name="submit" value="Post"  v-if="formType == 'store'" class="btn col-12 btn-outline-primary">
                    <input type="submit" name="submit" value="Update" v-if="formType == 'update'" class="btn  col-12 btn-outline-info">
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
    props: ['user', 'errors', 'formType', 'formContent', 'categories', 'tags', 'submitForm'],
    components: {
        ckeditor: CKEditor.component,
        Multiselect
    },
    data(){
        return {
            ckeditor: ClassicEditor,
            ckeditorConfig: {
                height: "1000",
                toolbar: {
                    items: [
                        'heading', '|',
                        'alignment', '|',
                        'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                        'link', '|',
                        'bulletedList', 'numberedList', 'todoList',
                        'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', '|',
                        'code', 'codeBlock', '|',
                        'insertTable', '|',
                        'clipboard', '|',
                        'outdent', 'indent', '|',
                        'uploadImage', 'blockQuote', '|',
                        'undo', 'redo', 'ckfinder',
                    ],
                    shouldNotGroupWhenFull: true
                },
                ckfinder: {
                    uploadUrl: route('ckfinder_connector')+"?command=QuickUpload&type=Files&responseType=json"
                },
                alignment: {
                    options: [ 'left', 'right', 'center' , 'justify']
                }

            },
            formData: {
                title: this.formContent.title,
                meta_title: this.formContent.meta_title,
                slug: this.formContent.slug,
                published_at: this.formContent.published_at,
                post_type: 'page',
                summary: '',
                content: this.formContent.content,
                thumbnail: this.formContent.thumbnail,
                author: this.user.id,
                post_status: this.formContent.post_status,
                categories: this.categories,
                tags: this.tags
            }
        }
    },
    mounted(){
    },
    methods: {
        update(){
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
        ,
        async fetchTags(query){
            let where = ''
            if (query) {
                where = {'name': query}
            }
            const response = await fetch(route('tag.get', where))
            const data = await response.json();
            const finalData = data.data.map((item) => {
                return { value: item.id, label: item.title }
            })
            return finalData;
        },
        async createTag(query){
            let where = ''
            if (query){
                where = {'title' : query}
            }
            axios.post(route('tag.insert'), where)
            await this.fetchTags(query)
        },
        uploadImage(event){
            const file = event.target.files[0];
            let data = new FormData();
            data.append('name', 'thumbnail');
            data.append('thumbnail', file)

            let config = {
                header : {
                    'Content-Type': file.type
                }
            }
            axios.post(route('post.fileupload'), data, config).then(response => {
                this.formData.thumbnail=response.data
            })
        }
    },
    computed: {
        slug(){
            const baseUrl = window.location.origin+"/page/";
            if (this.formData.title == null){
                return baseUrl
            }
            const title = this.formData.title.toLowerCase();
            const slugify = title
                .replace(/ /g,'_')
                .replace(/[^\w-]+/g,'');

            this.formData.slug = slugify.substr(0,30)
            this.formData.meta_title = this.formData.title;
            return baseUrl + slugify
        },
        summary(){
            if(this.formData.content != undefined){
                let htmlContent = new DOMParser().parseFromString(this.formData.content, 'text/html');
                let textContent = htmlContent.body.textContent;
                const maxLength = 500
                let trimmedString = textContent.substr(0, maxLength);
                trimmedString = trimmedString.substr(0, Math.min(trimmedString.length, trimmedString.lastIndexOf(" ")))
                this.formData.summary = trimmedString;
                return trimmedString;
            }else{
                return  "";
            }

        }
    }

}
</script>

<style>
.ck-editor__editable {
    min-height: 40vh;
}
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
