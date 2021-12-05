<template>
    <div>
        <inertia-link class="no-underline" :href="backUrl">
            <jet-button class="bg-gradient-to-r from-[#36AFAD] to-[#36C57F]">
                <font-awesome-icon
                    icon="backward"
                    size="md"
                    rotation="rotate"
                ></font-awesome-icon>
                Back
            </jet-button>
        </inertia-link>
    </div>
</template>

<script>
import JetButton from "./Button";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBackward } from "@fortawesome/free-solid-svg-icons";
library.add(faBackward);
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
