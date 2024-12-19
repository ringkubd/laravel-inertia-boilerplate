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
                            class="form-control h-20" 
                            v-model="messageText"
                            @keydown="onTyping"
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
        }
    }
}
</script>
