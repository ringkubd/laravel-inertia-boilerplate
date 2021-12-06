<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2>User Add</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1">
                <div class="card-header">
                    <Back :backUrl="route('users.index')"></Back>
                </div>
                <div class="card-body">
                    <formModule
                        :submitMethod="submit"
                        :isUpdate="false"
                        :user="user"
                        :errors="errors"
                        :madrasah="madrasah"
                    ></formModule>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import Back from "@/Shared/Back";
import formModule from "./Form";
export default {
    name: "Create",
    props: ["user", "errors", 'madrasah'],
    components: {
        BreezeAuthenticatedLayout,
        Back,
        formModule,
    },
    methods: {
        submit(formData) {
            this.$inertia.post(route("users.store"), formData);
        },
    },
};
</script>
