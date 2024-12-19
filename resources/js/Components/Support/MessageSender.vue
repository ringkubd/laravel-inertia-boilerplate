<template>
    <span class="font-bold border-b table-cell align-middle">
        {{ sender.name }}
        <small class="font-normal font-thin italic" style="font-size: 0.5rem">
            {{ formattedTime }}
        </small>
    </span>
</template>

<script>
import moment from 'moment';

export default {
    name: 'MessageSender',
    props: {
        sender: {
            type: Object,
            required: true
        },
        timestamp: {
            type: String,
            required: true
        }
    },
    computed: {
        formattedTime() {
            return moment(this.timestamp).fromNow();
        }
    }
}
</script>
