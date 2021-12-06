<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2>Role Add</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1">
                <div class="card-header">
                    <Back :backUrl="route('roles.index')"></Back>
                </div>
                <div class="card-body">
                    <formModule
                        :submitMethod="submit"
                        :isUpdate="false"
                        :role="role"
                        :errors="errors"
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
    props: ["role", "errors"],
    components: {
        BreezeAuthenticatedLayout,
        Back,
        formModule,
    },
    methods: {
        submit(formData) {
            this.$inertia.post(route("roles.store"), formData);
        },
    },
};
</script>
