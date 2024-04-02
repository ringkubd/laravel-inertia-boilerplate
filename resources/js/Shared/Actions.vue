<template>
    <div class="flex justify-center">
        <Link
            as="button"
            type="button"
            :href="editUrl"
            v-if="can.update"
            class="group"
        >
            <jet-button type="submit" class="hover:shadow-lg">
                <font-awesome-icon
                    icon="pen"
                    size="md"
                    rotation="rotate"
                    class="text-white"
                ></font-awesome-icon>
            </jet-button>
        </Link>

        <Link
            as="button"
            type="button"
            :href="detailUrl"
            v-if="detailUrl"
            class="group"
            target="_blank"
        >
            <jet-button type="submit" class="hover:shadow-lg bg-indigo-500">
                <font-awesome-icon
                    icon="info"
                    size="md"
                    rotation="rotate"
                    class="text-info"
                ></font-awesome-icon>
            </jet-button>
        </Link>

        <Link
            method="DELETE"
            as="button"
            type="button"
            class="group"
            :href="deleteUrl"
            v-if="can.delete"
        >
            <jet-button type="submit" class="bg-cyan-500 hover:shadow-lg">
                <font-awesome-icon
                    icon="trash"
                    size="md"
                    rotation="rotate"
                    class="text-red-600"
                ></font-awesome-icon>
            </jet-button>
        </Link>
        <slot></slot>
    </div>
</template>

<script>
import JetButton from "@/Shared/Button";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen, faTrash, faInfo } from "@fortawesome/free-solid-svg-icons";
library.add(faPen, faTrash, faInfo);

export default {
    name: "Actions",
    props: {
        editUrl: Boolean,
        deleteUrl: Boolean,
        isDetails: Boolean,
        detailUrl: Boolean,
        can: {
            type: Object,
            default: []
        }
    },
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
