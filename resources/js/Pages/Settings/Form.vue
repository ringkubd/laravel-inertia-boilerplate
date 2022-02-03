<template>
    <div>
        <Codemirror
            v-model:value="code"
            :options="cmOptions"
            class="min-h-screen"
            :height="700"
            :border="true"
        />
    </div>
</template>

<script>
import Codemirror from "codemirror-editor-vue3";
import "codemirror/mode/php/php.js";
import "codemirror/lib/codemirror.css";
import "codemirror/theme/dracula.css";
import { ref } from "vue";

export default {
    name: "CodeMirror",
    props: ['raw_code'],
    components: {Codemirror},
    data(){
        return {
            code:  ref(`${this.raw_code}`),
            cmOptions: {
                mode: "php", // Language mode
                theme: "dracula", // Theme
                lineNumbers: true, // Show line number
                smartIndent: true, // Smart indent
                indentUnit: 4, // The smart indent unit is 2 spaces in length
                foldGutter: true, // Code folding
                styleActiveLine: true, // Display the style of the selected row
                indentWithTabs: true,
                electricChars: true,
                spellcheck: true,
                autocorrect: true,
            },
        }
    }
}
</script>

<style scoped>

</style>
