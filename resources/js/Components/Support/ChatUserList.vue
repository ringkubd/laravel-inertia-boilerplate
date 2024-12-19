<template>
    <ul class="p-0.5 chat">
        <li 
            v-for="(sup, index) in support" 
            :key="sup.id"
            class="border-blue-100 border pl-1.5 mb-1.5 font-bold bg-blend-color hover:bg-green-200 shadow cursor-pointer"
            :class="{ 'chat-active': isActiveChat(sup) }"
            @click="$emit('chat-selected', sup)"
        >
            <span v-if="onlineUser[sup?.creator?.id]" class="font-extralight text-green-800">
                <font-awesome-icon icon="dot-circle" size="sm" class="text-green-600"/>
            </span>
            {{ sup?.creator?.name }}
            <sup v-if="unreadCount[sup.id]" class="animate-bounce rounded-full h-5 w-5 bg-green-200">
                {{ unreadCount[sup.id] }}
            </sup>
            <span 
                v-if="typing[sup?.creator.id]" 
                class="rounded-full animate-pulse overflow-scroll w-5 font-thin font-extralight text-blue-700" 
                style="font-size: .7em"
            >
                {{ truncate(typing[sup?.creator.id], 15) }}
            </span>
        </li>
    </ul>
</template>

<script>
import { truncate } from "@/Helpers/auth-header";

export default {
    name: 'ChatUserList',
    props: {
        support: {
            type: Array,
            required: true
        },
        activeConversation: {
            type: Object,
            required: true
        },
        onlineUser: {
            type: Object,
            required: true
        },
        unreadCount: {
            type: Object,
            required: true
        },
        typing: {
            type: Object,
            required: true
        }
    },
    methods: {
        isActiveChat(sup) {
            return this.activeConversation?.creator?.id === sup?.creator?.id;
        },
        truncate(str, n) {
            return truncate(str, n);
        }
    }
}
</script>
