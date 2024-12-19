<template>
    <ul class="p-0.5">
        <li 
            v-for="(message, index) in messages" 
            :key="message.id"
            class="border-blue-100 border pl-1.5 mb-1.5 shadow cursor-pointer"
            @focus="$emit('message-seen')"
        >
            <div class="row" @contextmenu="onContextMenu($event, message.id)">
                <template v-if="isCreatorMessage(message)">
                    <div class="col-3 table-cell align-middle">
                        <MessageSender :sender="message.sender" :timestamp="message.created_at" />
                    </div>
                    <MessageContent 
                        :message="message"
                        class="col-9 bg-blend-color py-1.5"
                    />
                </template>
                <template v-else>
                    <MessageContent 
                        :message="message"
                        class="col-9 bg-blend-color py-1.5"
                    />
                    <div class="col-3">
                        <MessageSender :sender="message.sender" :timestamp="message.created_at" />
                    </div>
                </template>
            </div>
        </li>
    </ul>
</template>

<script>
import moment from 'moment';
import MessageSender from './MessageSender.vue';
import MessageContent from './MessageContent.vue';

export default {
    name: 'ChatMessages',
    components: {
        MessageSender,
        MessageContent
    },
    props: {
        messages: {
            type: Array,
            required: true
        },
        conversationCreatorId: {
            type: Number,
            required: true
        }
    },
    methods: {
        isCreatorMessage(message) {
            return this.conversationCreatorId === message.sender.id;
        },
        onContextMenu(event, messageId) {
            event.preventDefault();
            this.$emit('context-menu', { event, messageId });
        }
    }
}
</script>
