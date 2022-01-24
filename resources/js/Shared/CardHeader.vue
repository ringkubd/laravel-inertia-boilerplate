<template>
    <div class="pt-0 flex flex-col md:flex-row">
        <div class="w-full md:w-1/12 flex-initial" v-if="create">
            <inertia-link type="button" as="button" class="btn bg-gradient-to-r from-[#36AFAD] to-[#36C57F]" :href="create" v-if="can.create && create">
                <font-awesome-icon
                    icon="plus"
                    size="md"
                    rotation="rotate"
                ></font-awesome-icon>
                Add
            </inertia-link>
        </div>
        <div class="w-full md:w-3/12 flex-initial">
            <slot name="first"></slot>
        </div>
        <div class="w-full md:w-3/12 flex-initial">
            <slot name="second"></slot>
        </div>
        <div class="w-full md:w-3/12 flex-initial">
            <slot name="third"></slot>
        </div>
        <div class="w-full md:w-3/12  flex-initial justify-content-end justify-end" v-if="searchMethod">
            <div class="form-group row w-full">
                <label for="search" class="col-sm-3 col-form-label">Search</label>
                <div class="col-sm-9">
                    <input
                        type="text"
                        id="search"
                        v-model="search_param"
                        @keyup="search"
                        class="rounded border form-control"
                    />
                </div>
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
