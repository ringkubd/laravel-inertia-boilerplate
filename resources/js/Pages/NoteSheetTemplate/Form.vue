<template>
    <form action="" @submit.prevent="submit">
        <div class="flex flex-row justify-center min-w-full min-vh-100">
            <div class="w-full md:w-1/2">
                <h2>Note sheet valid variable list.</h2>
                <ol class="list-decimal">
                    <li>[number_of_student]</li>
                    <li>[amount]</li>
                    <li>[section]</li>
                    <li>[subject]</li>
                    <li>[semester]</li>
                    <li>[bill_group]</li>
                    <li>[current_date]</li>
                    <li>[invoice_date]</li>
                    <li>[total_amount]</li>
                    <li>[idb_account]</li>
                    <li>[bill_period]</li>
                    <li>[session]</li>
                </ol>
            </div>
            <div class="w-full md:w-1/2">
                <div class="w-full form-group">
                    <label class="form-label" for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" v-model="form.title">
                </div>
                <div class="w-full form-group">
                    <label class="form-label" for="fee_type">Group</label>
                    <Multiselect
                        :autofocus="true"
                        placeholder="Choose Fee Type."
                        id="fee_type"
                        v-model="form.fee_type"
                        :options="fee_types"
                        :value="form.fee_type"
                        mode="multiple"
                        :searchable="true"
                        :filterResults="true"
                        class="form-control"
                        max="3"
                        :caret="true"
                    />
                </div>
                <div class="w-full form-group">
                    <label class="form-label" for="content">Content</label>
                    <ckeditor id="content" v-model="form.content" :editor="ckeditor" :config="ckeditorConfig"></ckeditor>
                </div>
                <div class="w-full form-group mt-3">
                    <input type="submit" v-if="formType === 'store'" class="bg-green-600 hover:shadow-xl px-3 py-2 rounded hover:border-double hover:border-2 hover:border-green-900">
                    <input type="submit" v-if="formType === 'update'" class="bg-green-600 hover:shadow-xl px-3 py-2 rounded hover:border-double hover:border-2 hover:border-green-900">
                </div>
            </div>
        </div>

    </form>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";
import CKEditor from "@ckeditor/ckeditor5-vue"
import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
import Multiselect from '@vueform/multiselect'

export default {
    name: "Form",
    props: ['submitForm', 'fee_types', 'formType', 'note_template', 'selected_fee_types'],
    components: {
        ckeditor: CKEditor.component,
        Multiselect
    },
    data(){
        return {
            ckeditor: ClassicEditor,
            ckeditorConfig: {
                height: "1600",
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
                        'undo', 'redo',
                    ],
                },
                alignment: {
                    options: [ 'left', 'right', 'center' , 'justify']
                }

            },
            form : useForm({
                title : this.note_template.title,
                content: this.note_template.content,
                fee_type: this.note_template.selected_fee_types
            })
        }
    },
    methods: {
        submit(){
            this.submitForm(this.form)
        },
        contentSelectChange(ele){
            var start = ele.target.selectionStart;
            var end = ele.target.selectionEnd;
            var fullText = ele.target.innerHTML;
            var selectText = ""
            if(start != end){
                selectText = fullText.substring(start, end)
                ele.target.innerHtml = fullText.replace(selectText, `<h2>${selectText}</h2>`)
            }
        }
    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
.ck-editor__editable {
    min-height: 40vh;
}
</style>
