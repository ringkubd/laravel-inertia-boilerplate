<template>
    <div class="flex justify-center">
        <inertia-link
            as="button"
            type="button"
            :href="editUrl"
            v-if="can.update"
            class="group"
        >
            <jet-button type="submit" class="animate-gradient-x bg-gradient-to-r text-black from-[#36AFAD] via-[#fde68a] to-[#36AFAD] hover:shadow-lg group-hover:animate-pulse">
                <font-awesome-icon
                    icon="pen"
                    size="md"
                    rotation="rotate"
                    class="text-black"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>

        <inertia-link
            as="button"
            type="button"
            :href="detailUrl"
            v-if="detailUrl"
            class="group"
            target="_blank"
        >
            <jet-button type="submit" class="animate-gradient-x bg-gradient-to-r from-[#198754] via-blue-300 to-[#46C57F] hover:shadow-lg group-hover:animate-pulse">
                <font-awesome-icon
                    icon="info"
                    size="md"
                    rotation="rotate"
                    class="text-black"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>

        <inertia-link
            method="DELETE"
            as="button"
            type="button"
            class="group"
            :href="deleteUrl"
            v-if="can.delete"
        >
            <jet-button type="submit" class="animate-gradient-x bg-gradient-to-r from-red-200 to-[#46C57F] hover:shadow-lg group-hover:animate-pulse">
                <font-awesome-icon
                    icon="trash"
                    size="md"
                    rotation="rotate"
                    class="text-red-500"
                ></font-awesome-icon>
            </jet-button>
        </inertia-link>
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
