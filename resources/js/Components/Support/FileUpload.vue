<template>
    <div class="relative border-dotted h-10 bg-gray-100 flex justify-center items-center">
        <div class="absolute">
            <div class="flex flex-col items-center">
                <i class="fa fa-folder-open fa-4x text-blue-700"></i>
                <span class="block text-gray-400 font-normal" v-html="fileName"></span>
            </div>
        </div>
        <input type="file" class="h-full w-full opacity-0" @change="onFileSelect" ref="fileInput">
    </div>
</template>

<script>
export default {
    name: 'FileUpload',
    data() {
        return {
            fileName: 'Attach your files here'
        };
    },
    methods: {
        onFileSelect() {
            const files = this.$refs.fileInput.files;
            this.fileName = files.length > 0 ? files[0].name : 'Attach your files here';
            this.$emit('file-selected', files[0]);
        },
        reset() {
            this.fileName = 'Attach your files here';
            this.$refs.fileInput.value = null;
        }
    }
}
</script>
