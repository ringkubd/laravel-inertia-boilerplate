<template>
    <div class="flex justify-center">
        <inertia-link as="button" type="button" :href="editUrl">
            <jet-button type="submit">
                <font-awesome-icon
                    icon="pen"
                    size="md"
                    rotation="rotate"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>

        <inertia-link
            as="button"
            type="button"
            :href="detailUrl"
            v-if="isDetails"
        >
            <jet-button type="submit" class="btn-success">
                <font-awesome-icon
                    icon="info"
                    size="md"
                    rotation="rotate"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>

        <inertia-link
            method="DELETE"
            as="button"
            type="button"
            :href="deleteUrl"
        >
            <jet-button type="submit" class="btn-danger">
                <font-awesome-icon
                    icon="trash"
                    size="md"
                    rotation="rotate"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>
    </div>
</template>

<script>
import JetButton from "@/Shared/Button";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faInfo);

export default {
    name: "Actions",
    props: ["editUrl", "deleteUrl", "isDetails", "detailUrl"],
    components: { JetButton },
    data() {
        return {
            isDetails: this.isDetails ?? false,
        };
    },
    mounted() {
        console.log();
    },
};
</script>

<style scoped></style>
