<template>
    <div class="flex h-screen justify-center mt-1">
        <form action="" method="" class="" @submit.prevent="submit">
            <div class="col form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    class="form-control"
                    v-model="formData.name"
                />
                <div v-if="errors.name" class="text-danger">
                    {{ errors.name }}
                </div>
            </div>
            <div class="col form-group">
                <label for="guard_name">Guard Name</label>
                <input
                    type="text"
                    class="form-control"
                    v-model="formData.guard_name"
                />
                <div v-if="errors.guard_name" class="text-danger">
                    {{ errors.guard_name }}
                </div>
            </div>
            <div class="col form-group flex justify-end mt-1">
                <input
                    v-if="isUpdate"
                    type="submit"
                    class="btn btn-block btn-secondary"
                    value="Update"
                />
                <input
                    v-else
                    type="submit"
                    class="btn btn-block btn-secondary"
                    value="Create"
                />
            </div>
        </form>
    </div>
</template>
<script>
export default {
    name: "form",
    props: ["role", "isUpdate", "submitMethod", "errors"],
    data() {
        return {
            formData: {
                name: this.role.name,
                guard_name: this.role.guard_name,
            },
        };
    },
    methods: {
        submit() {
            this.submitMethod(this.formData);
        },
    },
};
</script>
