<template>
    <div class="flex h-screen justify-center mt-5">
        <form action="" method="" class="w-2/4" @submit.prevent="submit">
            <div class="col form-group">
                <label for="name">Name</label>
                <div class="row">
                    <div class="col-8">
                        <input
                            type="text"
                            class="form-control"
                            v-model="formData.name"
                        />
                    </div>
                    <div class="col-4">
                        <div class="text-success">
                            {{permissionName}}
                        </div>
                    </div>
                </div>
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

            <div class="col form-group">
                <label for="module">Module</label>
                <Multiselect
                    v-model="formData.module"
                    placeholder="Choose Module"
                    :filterResults="true"
                    :minChars="1"
                    :resolveOnLoad="true"
                    :delay="0"
                    :searchable="true"
                    mode="single"
                    limit="50"
                    :options="modules"
                />
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
import Multiselect from '@vueform/multiselect'
export default {
    name: "form",
    props: ["permission", "isUpdate", "submitMethod", "errors", 'modules'],
    components: {
        Multiselect
    },
    data() {
        return {
            formData: {
                name: this.permission.name,
                guard_name: this.permission.guard_name,
                module: this.permission.module
            },
        };
    },
    methods: {
        submit() {
            this.submitMethod(this.formData);
        },
    },
    computed: {
        permissionName(){
            const splitName = this.formData.name != null ? this.formData.name.split("_") : "";
            if (splitName.length < 2 && this.formData.name != null){
                this.name = this.formData.name +"_"+ this.formData.module
                return this.name
            }
            return this.name;

        }
    }
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>

</style>
