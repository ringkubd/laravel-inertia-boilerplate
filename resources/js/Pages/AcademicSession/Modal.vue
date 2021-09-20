<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">

                <div class="modal-header">
                    <slot name="header">
                        default header
                    </slot>
                </div>

                <div class="modal-body">
                    <slot name="body">
                        default body
                    </slot>
                </div>

                <div class="modal-footer">
                    <slot name="footer">
                        <button class="modal-default-button" @click="$emit('close')">
                            <font-awesome-icon
                                icon="times"
                                size="md"
                                rotation="rotate"
                                class="text-red-500"
                            ></font-awesome-icon>
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimes } from "@fortawesome/free-solid-svg-icons";
library.add(faTimes);
export default {
    name: "Modal",
    props: {
      showModal: {
          type: Boolean,
          default: false
      }
    },
    data() {
        return {
            showModal: this.showModal,
        }
    },
    methods: {

    }
}
</script>

<style scoped>

</style>
