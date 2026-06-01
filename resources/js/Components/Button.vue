<template>
    <button :type="type" class="
    inline-flex
    items-center px-4 py-2
    bg-brand-600 border border-transparent
     rounded-md font-semibold
     text-xs text-white
     hover:bg-brand-700
     active:bg-brand-800 focus:outline-none
     focus:ring-2 focus:ring-brand-300 focus:ring-offset-2
">
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'submit',
        },
    }
}
</script>
