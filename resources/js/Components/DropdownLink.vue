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
                ? 'block w-full no-underline px-4 py-3 text-left text-sm font-medium leading-5 text-brand-700 bg-brand-50 focus:outline-none focus:bg-brand-100 transition duration-150 ease-in-out'
                : 'block w-full no-underline px-4 py-3 text-left text-sm leading-5 text-gray-700 hover:bg-brand-50 focus:outline-none focus:bg-brand-50 transition duration-150 ease-in-out'
        }
    }
}
</script>
