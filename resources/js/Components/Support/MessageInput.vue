<template>
    <div class="card-footer static bottom-0" style="width: 100%">
        <span v-if="typing">
            <b>{{ typingUser.name }} <i>typing..</i>:</b> {{ typingText }}
        </span>
        <div class="row">
            <div class="col-10">
                <form @submit.prevent="onSubmit">
                    <div class="form-group">
                        <textarea
                            class="form-control"
                            style="min-height: 40px; max-height: 200px;"
                            v-model="messageText"
                            @keydown="onTyping"
                            @input="autoResizeTextarea"
                            @paste="autoResizeTextarea"
                            ref="messageInput"
                        ></textarea>
                    </div>
                    <FileUpload
                        ref="fileUpload"
                        @file-selected="onFileSelected"
                    />
                    <input type="submit" class="btn btn-success" value="Send">
                </form>
            </div>
            <div class="col-2">
                <button
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-black py-2 px-4 border border-blue-400 border-2 hover:border-transparent rounded"
                    @click="$emit('like-clicked')"
                >
                    <font-awesome-icon icon="thumbs-up" size="lg" class="text-success"/>
                </button>
                <button
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-black py-2 px-4 border border-blue-400 border-2 hover:border-transparent rounded"
                    @click="$emit('done-clicked')"
                >
                    <font-awesome-icon icon="check" size="lg" class="text-info"/>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import FileUpload from './FileUpload.vue';

export default {
    name: 'MessageInput',
    components: {
        FileUpload
    },
    props: {
        typing: Boolean,
        typingUser: Object,
        typingText: String
    },
    mounted() {
        // Initialize textarea height
        this.$nextTick(() => {
            if (this.$refs.messageInput) {
                this.autoResizeTextarea();
            }
        });
    },

    watch: {
        // Watch for message clearing to reset textarea height
        messageText(newVal) {
            if (newVal === '') {
                const textarea = this.$refs.messageInput;
                if (textarea) {
                    textarea.style.height = '40px'; // Reset to default height
                }
            }
        }
    },
    data() {
        return {
            messageText: '',
            selectedFile: null
        }
    },
    methods: {
        onSubmit() {
            if (!this.isValidInput()) return;

            const formData = new FormData();
            if (this.selectedFile) {
                formData.append('attachment', this.selectedFile);
            }
            formData.append('message', this.messageText);

            this.$emit('message-submit', formData);
            this.resetForm();
        },
        onTyping() {
            this.$emit('typing', this.messageText);
        },
        onFileSelected(file) {
            this.selectedFile = file;
        },
        isValidInput() {
            return this.messageText.trim() !== '' || this.selectedFile;
        },
        resetForm() {
            this.messageText = '';
            this.selectedFile = null;
            this.$refs.fileUpload.reset();

            // Reset textarea height
            if (this.$refs.messageInput) {
                this.$refs.messageInput.style.height = '40px';
            }
        },
        autoResizeTextarea(event) {
            const textarea = this.$refs.messageInput;
            if (!textarea) return;

            // For paste events, add a small delay to ensure content is fully processed
            if (event && event.type === 'paste') {
                setTimeout(() => this.performResize(textarea), 10);
            } else {
                this.performResize(textarea);
            }
        },

        performResize(textarea) {
            // Reset height to auto first to get accurate scrollHeight
            textarea.style.height = 'auto';

            // Calculate new height with some padding to prevent scrollbars
            const newHeight = Math.min(Math.max(textarea.scrollHeight, 40), 200); // Min 40px, max 200px
            textarea.style.height = `${newHeight}px`;
        }
    }
}
</script>
