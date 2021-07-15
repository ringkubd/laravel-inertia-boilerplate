<template>
    <div>
        <inertia-link class="no-underline" :href="backUrl">
            <jet-button>Back</jet-button>
        </inertia-link>
    </div>
</template>

<script>
import JetButton from "./Button";
export default {
    name: "Back",
    props: ["backUrl"],
    components: {
        JetButton,
    },
    data() {
        return {
            backUrl: this.backUrl,
        };
    },
};
</script>

<style scoped></style>
