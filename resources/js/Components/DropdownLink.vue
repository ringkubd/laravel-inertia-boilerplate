<template>
    <Link :class="classes">
        <slot />
    </Link>
</template>
<script>
import { Link } from '@inertiajs/vue3'
export default {
    props: ['active'],
    components: {
        Link
    },
    computed: {
        classes() {
            return this.active
                ? 'block w-full shadow-lg no-underline px-4 rounded py-2 text-left text-sm bg-green-200 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out no-underline'
                : 'block w-full no-underline px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out'
        }
    }
}
</script>
