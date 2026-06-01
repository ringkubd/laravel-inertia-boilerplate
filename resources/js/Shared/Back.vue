<template>
    <div>
        <Link class="no-underline" :href="backUrl">
            <jet-button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                <font-awesome-icon
                    icon="backward"
                    size="md"
                    rotation="rotate"
                    class="text-gray-500"
                ></font-awesome-icon>
                <span> Back</span>
            </jet-button>
        </Link>
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
