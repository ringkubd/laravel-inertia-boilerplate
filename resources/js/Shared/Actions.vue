<template>
    <div class="flex justify-center">
        <Link
            as="button"
            type="button"
            :href="editUrl"
            v-if="can.update"
            class="group"
        >
            <jet-button type="submit" class="hover:shadow-lg bg-blue-500">
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
            <jet-button type="submit" class="hover:shadow-lg bg-green-500">
                <font-awesome-icon
                    icon="info"
                    size="md"
                    rotation="rotate"
                    class="text-white"
                ></font-awesome-icon>
            </jet-button>
        </Link>

        <button
            type="button"
            class="group bg-transparent border-0 p-0"
            @click="confirmDelete"
            v-if="can.delete && deleteUrl"
        >
            <jet-button type="submit" class="bg-red-500 hover:shadow-lg">
                <font-awesome-icon
                    icon="trash"
                    size="md"
                    rotation="rotate"
                    class="text-white"
                ></font-awesome-icon>
            </jet-button>
        </button>
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
    inject: {
        confirm: {
            from: 'confirm',
            default: null,
        },
    },
    data() {
        return {
            isDetails: this.isDetails ?? false,
        };
    },
    methods: {
        confirmDelete() {
            if (!this.confirm) {
                if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    this.$inertia.delete(this.deleteUrl);
                }
                return;
            }
            this.confirm.show('Are you sure you want to delete this item? This action cannot be undone.')
                .then((confirmed) => {
                    if (confirmed) {
                        this.$inertia.delete(this.deleteUrl);
                    }
                });
        },
    },
};
</script>
