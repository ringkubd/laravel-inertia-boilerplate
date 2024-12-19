<template>
    <div>
        <img v-if="message.message === '@like@'" 
            src="/like.png" 
            class="image w-1/12 flex content-center"
        >
        <span v-else>{{ message.message }}</span>
        
        <vue-picture-swipe 
            v-if="isImage" 
            :items="imageItems" 
        />
        
        <a v-else-if="message.attachment_type !== null" 
           :href="message.attachment" 
           target="_blank"
        >
            <font-awesome-icon
                icon="paperclip"
                size="md"
                class="text-info"
            />
            Attachment
        </a>
    </div>
</template>

<script>
export default {
    name: 'MessageContent',
    props: {
        message: {
            type: Object,
            required: true
        }
    },
    computed: {
        isImage() {
            return String(this.message.attachment_type).search('image') !== -1;
        },
        imageItems() {
            if (!this.message.attachment) return [];
            
            return [{
                src: this.message.attachment,
                thumbnail: this.message.attachment,
                w: 1200,
                h: 900,
                title: 'Message Attachment'
            }];
        }
    }
}
</script>
