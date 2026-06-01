<template>
    <div class="flex justify-center flex-wrap gap-1">
        <Link
            as="button"
            type="button"
            :href="editUrl"
            v-if="can.update"
            class="group"
        >
            <jet-button type="submit" class="!bg-brand-500 hover:!bg-brand-600 !px-2 !py-1">
                <font-awesome-icon
                    icon="pen"
                    size="sm"
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
            <jet-button type="submit" class="!bg-accent-500 hover:!bg-accent-600 !px-2 !py-1">
                <font-awesome-icon
                    icon="info"
                    size="sm"
                    rotation="rotate"
                    class="text-white"
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
            <jet-button type="submit" class="!bg-red-500 hover:!bg-red-600 !px-2 !py-1">
                <font-awesome-icon
                    icon="trash"
                    size="md"
                    rotation="rotate"
                    class="text-white"
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
