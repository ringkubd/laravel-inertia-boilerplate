<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3'
    export default {
        props: ['href', 'active'],
        components: {
            Link
        },
        computed: {
            classes() {
                return this.active
                            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-brand-400 transition duration-150 ease-in-out no-underline'
                            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white/80 hover:text-white hover:border-white/30 focus:outline-none focus:text-white focus:border-white/30 transition duration-150 ease-in-out no-underline'
            }
        }
    }
</script>
