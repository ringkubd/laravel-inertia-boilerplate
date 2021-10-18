<template>
<div :class="classes">
    <div class="flex">
        <div flex="flex-inline block">
            <slot></slot>
        </div>
        <div v-if="online">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
                <circle r="5" cx="6" cy="6" style="fill:Green;stroke:gray;stroke-width:0.1"/>
            </svg>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "friend",
    props: ['active', 'online'],
    computed: {
        classes(){
            return this.active
                ? 'block cursor-pointer w-full no-underline border-b-2 border-indigo-400 px-4 py-2 text-left text-sm bg-green-50 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out no-underline'
                : 'block cursor-pointer w-full no-underline border-b-2  px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-indigo-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out'
        }
    }
}
</script>

<style scoped>

</style>
