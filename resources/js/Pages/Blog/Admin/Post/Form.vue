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
                            <label for="slug" class="form-label">Slug</label>
                            <input type="hidden" class="form-control" id="slug" v-model="formData.slug">
                            <a :href="slug" class="form-control"> {{slug}}</a>
                            <div v-if="errors.title" class="text-danger">
                                {{ errors.title }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="title" class="form-label">Post Content</label>
                            <ckeditor v-model="formData.content" :editor="ckeditor" :config="ckeditorConfig"></ckeditor>
                            <div v-if="errors.title" class="text-danger">
                                {{ errors.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import CKEditor from "@ckeditor/ckeditor5-vue"
import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
export default {
    name: "Form",
    props: ['user', 'errors'],
    components: {
        ckeditor: CKEditor.component,
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
                post_status: ""
            }
        }
    },
    methods: {
        submit(){

        }
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
