<template>
    <div>
        <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex w-full px-4">
            <div class="relative w-full max-w-lg my-6 mx-auto">
                <div class="border-0 rounded-xl shadow-xl relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <slot name="header"></slot>
                        </h3>
                        <button class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" v-on:click="toggleModal()">
                            <span class="text-xl leading-none">&times;</span>
                        </button>
                    </div>
                    <div class="relative px-6 py-4">
                        <slot name="body"></slot>
                    </div>
                    <div v-if="$slots.footer" class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 rounded-b">
                        <slot name="footer"></slot>
                        <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition" type="button" v-on:click="toggleModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="opacity-30 fixed inset-0 z-40 bg-black"></div>
    </div>
</template>

<script>
export default {
    name: "Modal",
    props: ['openModal', 'closeModal'],
    data() {
        return {
            showModal: this.openModal
        }
    },
    methods: {
        toggleModal(){
            this.showModal = false;
           this.closeModal()
        }
    },
    computed: {
        showModal() {
            this.showModal = this.openModal;
            return this.showModal;
        }
    }
}
</script>

<style scoped>

</style>
