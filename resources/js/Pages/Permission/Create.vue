<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2>Permission Add</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1">
                <div class="card-header">
                    <Back :backUrl="route('permission.index')"></Back>
                </div>
                <div class="card-body">
                    <formModule
                        :submitMethod="submit"
                        :isUpdate="false"
                        :permission="permission"
                        :errors="errors"
                        :modules="modules"
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
    props: ["permission", "errors", 'modules'],
    components: {
        BreezeAuthenticatedLayout,
        Back,
        formModule,
    },
    methods: {
        submit(formData) {
            this.$inertia.post(route("permission.store"), formData);
        },
    },
};
</script>
