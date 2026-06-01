<template>
    <div class="p-2 space-y-3 sm:space-y-2">
        <!-- Top row - Mobile: Stacked, Desktop: Side by side -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <!-- Add Button -->
            <div class="flex items-center" v-if="create">
                <Link
                    type="button"
                    as="button"
                    class="btn bg-gradient-to-r from-[#36AFAD] to-[#36C57F] hover:opacity-90 transition-opacity flex items-center gap-2 w-full sm:w-auto justify-center"
                    :href="create"
                    v-if="can.create && create"
                >
                    <font-awesome-icon
                        icon="plus"
                        size="sm"
                    ></font-awesome-icon>
                    <span>Add</span>
                </Link>
            </div>

            <!-- Search Field - Moves to top right on desktop -->
            <div class="w-full sm:w-auto sm:ml-auto" v-if="searchMethod">
                <div class="flex items-center gap-2">
                    <label for="search" class="whitespace-nowrap text-sm font-medium">Search:</label>
                    <div class="relative w-full">
                        <input
                            type="text"
                            id="search"
                            v-model="search_param"
                            @keyup="search"
                            placeholder="Search..."
                            class="rounded border form-control w-full px-3 py-2 text-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Extra slot for additional buttons/actions -->
            <div class="flex items-center justify-end mt-2 sm:mt-0">
                <slot name="extra"></slot>
            </div>
        </div>

        <!-- Filters section - 3 columns on desktop, stacked on mobile -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="w-full">
                <slot name="first"></slot>
            </div>
            <div class="w-full">
                <slot name="second"></slot>
            </div>
            <div class="w-full">
                <slot name="third"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import Button from "./Button";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
library.add(faPlus);

export default {
    name: "CardHeader",
    props: {
        create: Boolean,
        searchMethod: Function,
        can: {
            type: Object,
            default: () => ({ create: false })
        }
    },
    components: {
        Button,
    },
    data() {
        return {
            search_param: GET('search')[0] || '',
        };
    },
    methods: {
        search() {
            this.searchMethod(this.search_param);
        },
    },
};
</script>

<style scoped>
/* Add any additional custom styles here */
.form-control:focus {
    @apply ring-2 ring-[#36AFAD] outline-none;
}
</style>
