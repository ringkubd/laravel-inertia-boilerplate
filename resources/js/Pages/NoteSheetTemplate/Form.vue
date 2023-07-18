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
                    <li>[mma_table]</li>
                    <li>[admission_table]</li>
                    <li>[sem_table]</li>
                    <li>[circular_date]</li>
                    <li>[eligible_student]</li>
                    <li>[invoice_month_year]</li>
                    <li>[failed_student]</li>
                    <li>[prev_semester]</li>
                    <li>[dakhil_exam_student]</li>
                    <li>[dakhil_pass_student]</li>
                    <li>[dakhil_exam_year]</li>
                    <li>[admission_selected_student]</li>
                    <li>[admission_enrolled_student]</li>
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
                    <!--                    <Ckeditor id="content" v-model="form.content" :editor="editor" :config="ckeditorConfig"></Ckeditor>-->
                    <QuillEditor
                        theme="snow"
                        v-model:content="form.content"
                        :content="form.content"
                        contentType="html"
                        :modules="modules"
                    />
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
import Multiselect from '@vueform/multiselect'
import {Delta, QuillEditor} from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import QuillBetterTable from "quill-better-table";
import htmlEditButton from 'quill-html-edit-button';
import * as QuillSmartPaste from 'quill-paste-smart';
const MSwordMatcher = function (node, delta) {

    const _build = [];

    while (true) {

        if (node) {

            if (node.tagName === 'P') {

                const content = node.querySelectorAll('span'); //[0] index contains bullet or numbers, [1] index contains spaces, [2] index contains item content
                const _nodeText = content[2].innerText.trim();
                //const _listType = content[0].innerText.match(/[0-9]/g) ? 'ordered' : 'bullet'; //@TODO: implement ordered lists

                _build.push({ insert: `${_nodeText}\n`, attributes: { 'bullet': true } });

                if (node.className === 'MsoListParagraphCxSpLast') {
                    break;
                }

            }
        }

        node = node.nextSibling;

    }

    return new Delta(_build);

};
const matcherNoop = (node, delta) => ({ ops: [] });

function matchMsWordList(node, delta) {
    // Clone the operations
    let ops = delta.ops.map((op) => Object.assign({}, op));

    // Trim the front of the first op to remove the bullet/number
    let bulletOp = ops.find((op) => op.insert && op.insert.trim().length);
    if (!bulletOp) { return delta }

    bulletOp.insert = bulletOp.insert.trimLeft();
    let listPrefix = bulletOp.insert.match(/^.*(^·|\.)/) || bulletOp.insert[0];
    bulletOp.insert = bulletOp.insert.substring(listPrefix[0].length, bulletOp.insert.length);

    // Trim the newline off the last op
    let last = ops[ops.length-1];
    last.insert = last.insert.substring(0, last.insert.length - 1);

    // Determine the list type
    let listType = listPrefix[0].length === 1 ? 'bullet' : 'ordered';

    // Determine the list indent
    let style = node.getAttribute('style').replace(/\n+/g, '');
    let levelMatch = style.match(/level(\d+)/);
    let indent = levelMatch ? levelMatch[1] - 1 : 0;

    // Add the list attribute
    ops.push({insert: '\n', attributes: {list: listType, indent}})

    return new Delta(ops);
}


const MSWORD_MATCHERS = [
    ['p.MsoListParagraphCxSpFirst', matchMsWordList],
    ['p.MsoListParagraphCxSpMiddle', matchMsWordList],
    ['p.MsoListParagraphCxSpLast', matchMsWordList],
    ['p.msolistparagraph', matchMsWordList]
];


export default {
    name: "Form",
    props: ['submitForm', 'fee_types', 'formType', 'note_template', 'selected_fee_types'],
    components: {
        Multiselect,
        QuillEditor
    },
    data() {
        return {
            form: useForm({
                title: this.note_template.title,
                content: this.note_template.content ?? `<h2>Hi</h2>`,
                fee_type: this.selected_fee_types
            }),
            toolbarOptions: [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['blockquote', 'code-block'],

                [{'list': 'ordered'}, {'list': 'bullet'}],
                [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
                [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
                [{'direction': 'rtl'}],                         // text direction

                [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
                [{'header': [1, 2, 3, 4, 5, 6, false]}],

                [{'color': []}, {'background': []}],          // dropdown with defaults from theme
                [{'font': []}],
                [{'align': []}],

                ['clean']                                         // remove formatting button
            ],
            modules: [
                {
                    name: 'quillBetterTable',
                    module: QuillBetterTable,
                    operationMenu: {
                        items: {
                            unmergeCells: {
                                text: 'Another unmerge cells name'
                            }
                        }
                    }
                },
                {
                    name: 'smartPaste',
                    module: QuillSmartPaste,
                },
                {
                    name: 'htmlEditButton',
                    module: htmlEditButton,
                    options: {
                        debug: true, // logging, default:false
                        msg: "Edit the content in HTML format", //Custom message to display in the editor, default: Edit HTML here, when you click "OK" the quill editor's contents will be replaced
                        okText: "Ok", // Text to display in the OK button, default: Ok,
                        cancelText: "Cancel", // Text to display in the cancel button, default: Cancel
                        buttonHTML: "&lt;&gt;", // Text to display in the toolbar button, default: <>
                        buttonTitle: "Show HTML source", // Text to display as the tooltip for the toolbar button, default: Show HTML source
                        syntax: false, // Show the HTML with syntax highlighting. Requires highlightjs on window.hljs (similar to Quill itself), default: false
                        prependSelector: 'div#myelement', // a string used to select where you want to insert the overlayContainer, default: null (appends to body),
                        editorModules: {} // The default mod
                    },
                }
            ],
            methods: {
                submit() {
                    this.submitForm(this.form)
                },
                contentSelectChange(ele) {
                    var start = ele.target.selectionStart;
                    var end = ele.target.selectionEnd;
                    var fullText = ele.target.innerHTML;
                    var selectText = ""
                    if (start != end) {
                        selectText = fullText.substring(start, end)
                        ele.target.innerHtml = fullText.replace(selectText, `<h2>${selectText}</h2>`)
                    }
                }
            },
            setup: () => {

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
