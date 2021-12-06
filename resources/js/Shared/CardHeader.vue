<template>
    <div class="pt-0 grid md:grid-cols-4 flex">
        <div class="flex-1" v-if="create">
            <inertia-link type="button" as="button" :href="create" v-if="can.create && create"
                >
                <jet-button type="button" class="btn animate-gradient-x bg-gradient-to-r from-[#36AFAD] via-yellow-200 to-[#36C57F]">
                    <font-awesome-icon
                        icon="plus"
                        size="md"
                        rotation="rotate"
                    ></font-awesome-icon>
                    Add New
                </jet-button>
            </inertia-link>
        </div>
        <div class="flex-1">
            <slot name="first"></slot>
        </div>
        <div class="flex-1">
            <slot name="second"></slot>
        </div>
        <div class="flex-1">
            <slot name="third"></slot>
        </div>
        <div class="flex-1 col-start-5 justify-end" v-if="searchMethod">
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
    props: {
        create: Boolean,
        searchMethod: Function,
        can: {
            type: Object,
            default: []
        }
    },
    components: {
        Button,
    },
    data() {
        return {
            search_param: GET('search')[0],
        };
    },
    methods: {
        search() {
            this.searchMethod(this.search_param);
        },
    },
};
</script>
