<template>
    <div>
        <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex w-full">
            <div class="relative w-auto my-6 mx-auto min-w-max">
                <!--content-->
                <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                    <!--header-->
                    <div class="flex items-center justify-center py-0 px-2 border-b border-solid border-blue-200 rounded text-center">
                        <h3 class="text-3xl font-semibold m-2">
                            <slot name="header"></slot>
                        </h3>
                        <button class="ml-auto bg-transparent border-0 text-black float-right text-3xl leading-none font-semibold" v-on:click="toggleModal()">
                            <span class="bg-transparent text-red-600 text-2xl block outline-none focus:outline-none">×</span>
                        </button>
                    </div>
                    <!--body-->
                    <div class="relative p-6 flex-auto">
                        <slot name="body"></slot>
                    </div>
                    <!--footer-->
                    <div class="flex items-center justify-end border-t border-solid border-blueGray-200 rounded">
                        <slot name="footer"></slot>
                        <button class="text-red-800 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
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
