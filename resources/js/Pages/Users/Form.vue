<template>
    <div class="flex h-screen justify-center mt-5">
        <form action="" method="" class="w-1/3" @submit.prevent="submit">
            <div class="col form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="formData.name"
                    ref="name"
                    @blur="onConfirm"
                />
                <div v-if="errors.name" class="text-danger">
                    {{ errors.name }}
                </div>
            </div>
            <div class="col form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    v-model="formData.email"
                    ref="email"
                    @blur="onConfirm"
                />
                <div v-if="errors.email" class="text-danger">
                    {{ errors.email }}
                </div>
            </div>
            <div class="col form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    v-model="formData.password"
                    ref="password"
                    @blur="onConfirm"
                />
                <div v-if="errors.password" class="text-danger">
                    {{ errors.password }}
                </div>
            </div>
            <div class="col form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="confirmPassword"
                    v-model="formData.password_confirmation"
                    ref="password_confirmation"
                    @blur="onConfirm"
                />
                <div v-if="errors.password_confirmation" class="text-danger">
                    {{ errors.password_confirmation }}
                </div>
            </div>
            <div class="col form-group">
                <label for="roles">Roles</label>
                <Multiselect
                    v-model="formData.roles"
                    placeholder="Choose Roles"
                    :filterResults="true"
                    :minChars="1"
                    :resolveOnLoad="true"
                    :delay="0"
                    :searchable="true"
                    mode="multiple"
                    limit="50"
                    :loading="true"
                    id="roles"
                    :options="async function(query) {
                                                return await fetchRoles(query)
                                              }"

                />
                <div v-if="errors.roles" class="text-danger">
                    {{ errors.roles }}
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
import Multiselect from '@vueform/multiselect'
export default {
    name: "form",
    props: ["user", "isUpdate", "submitMethod", "errors"],
    components: {
        Multiselect
    },
    data() {
        return {
            formData: {
                name: this.user.name,
                email: this.user.email,
                roles: this.user.roles != null ? this.user.roles.map((item) => {
                    return item.id
                }) : []
            },
            testRoles: [{label: 'Super Admin', value: 170206}]
        };
    },
    methods: {
        submit() {
            if(this.onConfirm()){
                this.submitMethod(this.formData);
            }
        },
        onConfirm(){
            const name = this.$refs.name.value;
            const email = this.$refs.email.value;
            const password = this.$refs.password.value;
            const confirmPassword = this.$refs.password_confirmation.value;

            if (name.length === 0){
                this.errors.name = "Name can't blank.";
                return false;
            }
            this.errors.name = false;
            if (email.length === 0){
                this.errors.email = "Email can't blank.";
                return false;
            }
            this.errors.email = false;

            if (password.length === 0){
                this.errors.password = "Password can't blank.";
                return false;
            }
            this.errors.password = false;

            if (confirmPassword.length === 0){
                this.errors.password_confirmation = "Confirm password can't blank.";
                return false;
            }
            if (password !== confirmPassword){
                this.errors.password_confirmation = "Password doesn't match";
                return false;
            }
            this.errors.password_confirmation = false;
            return true;
        },
        async fetchRoles(query){
            let where = ''

            if (query) {
                where = {'name': query}
            }
            const response = await fetch(route('roles.get', where))

            const data = await response.json();
            const finalData = data.data.map((item) => {
                return { value: item.id, label: item.name }
            })
            return finalData;
        },
    }
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>

</style>
