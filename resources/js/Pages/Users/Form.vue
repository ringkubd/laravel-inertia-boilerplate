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
    props: ["user", "isUpdate", "submitMethod", "errors"],
    data() {
        return {
            formData: {
                name: this.user.name,
                email: this.user.email,
            },
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
        }
    },
};
</script>
