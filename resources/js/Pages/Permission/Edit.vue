<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2>Permission Edit</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1">
                <div class="card-header">
                    <Back :backUrl="route('permission.index')"></Back>
                </div>
                <div class="card-body">
                    <formModule
                        :submitMethod="submit"
                        :isUpdate="true"
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
    name: "Edit",
    props: ["permission", "errors", 'modules'],
    components: {
        BreezeAuthenticatedLayout,
        Back,
        formModule,
    },
    methods: {
        submit(formData) {
            this.$inertia.put(
                route("permission.update", this.permission.id),
                formData
            );
            console.log(this.errors);
        },
    },
};
</script>
