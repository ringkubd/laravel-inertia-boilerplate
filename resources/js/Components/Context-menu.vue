<template>
    <div class="context-menu" v-show="show" :style="style" ref="context" tabindex="0" @blur="close">
        <slot></slot>
    </div>
</template>
<script>
import {nextTick} from 'vue';

export default {
    name: 'CmpContextMenu',
    props: {
        display: Boolean, // prop detect if we should show context menu
    },
    data() {
        return {
            left: 0, // left position
            top: 0, // top position
            show: false, // affect display of context menu
        };
    },
    computed: {
        style() {
            return {
                top: this.top + 'px',
                left: this.left + 'px',
            };
        },
    },
    methods: {
        close() {
            this.show = false;
            this.left = 0;
            this.top = 0;
        },
        open(evt) {
            // updates position of context menu
            this.left = evt.pageX || evt.clientX;
            this.top = evt.pageY || evt.clientY;
            // make element focused
            // @ts-ignore
            nextTick(() => this.$el.focus());
            this.show = true;
        },
    },
};
</script>
<style>
.context-menu {
    position: fixed;
    background: white;
    z-index: 999;
    outline: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    cursor: pointer;
}
</style>
