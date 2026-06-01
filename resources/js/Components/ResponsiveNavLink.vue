<template>
    <Link :class="classes">
        <slot />
    </Link>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return this.active
                            ? 'block pl-3 pr-4 py-3 border-l-4 border-brand-500 text-base font-medium text-brand-700 bg-brand-50 focus:outline-none focus:text-brand-800 focus:bg-brand-100 focus:border-brand-600 transition duration-150 ease-in-out'
                            : 'block pl-3 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-white/80 hover:text-white hover:bg-white/10 hover:border-white/30 focus:outline-none focus:text-white focus:bg-white/10 focus:border-white/30 transition duration-150 ease-in-out'
            }
        }
    }
</script>
