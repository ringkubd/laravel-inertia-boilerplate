<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2>Role Edit</h2>
        </template>
        <div class="container-fluid">
            <div class="card mt-1">
                <div class="card-header">
                    <Back :backUrl="route('roles.index')"></Back>
                </div>
                <div class="card-body">
                    <formModule
                        :submitMethod="submit"
                        :isUpdate="true"
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
    name: "Edit",
    props: ["role", "errors"],
    components: {
        BreezeAuthenticatedLayout,
        Back,
        formModule,
    },
    methods: {
        submit(formData) {
            this.$inertia.put(route("roles.update", this.role.id), formData);
            console.log(this.errors);
        },
    },
};
</script>
