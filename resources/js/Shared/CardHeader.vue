<template>
    <div class="pt-0 grid md:grid-cols-4">
        <div class="flex-col">
            <inertia-link type="button" as="button" :href="create"
                ><jet-button type="button">
                    <font-awesome-icon
                        icon="plus"
                        size="md"
                        rotation="rotate"
                    ></font-awesome-icon>
                    Add New
                </jet-button>
            </inertia-link>
        </div>
        <div class="flex-col">
            <slot name="first"></slot>
        </div>
        <div class="flex-col">
            <slot name="second"></slot>
        </div>
        <div class="flex col-start-4 justify-end">
            <label for="search">Search</label>
            <input
                type="text"
                id="search"
                v-model="search_param"
                @keyup="search"
                class="ml-2 py-1 text-sm rounded border"
            />
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
    props: ["index", "create", "searchMethod"],
    components: {
        Button,
    },
    data() {
        return {
            search_param: "",
        };
    },
    methods: {
        search() {
            this.searchMethod(this.search_param);
        },
    },
};
</script>
