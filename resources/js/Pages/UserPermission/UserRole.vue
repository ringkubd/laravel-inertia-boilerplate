<template>
    <authenticated-layout>
        <template #header>
            <h2>User Role Management</h2>
        </template>
        <div class="container-fluid">
            <div class="flex h-screen w-screen justify-center">
                <div class="card w-2/3">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <form action="" @submit.prevent="submitForm">
                            <div class="row mb-3">
                                <label for="user" class="col-sm-2 col-form-label">User</label>
                                <div class="col-sm-10">
                                    <Multiselect
                                        v-model="selectedUser"
                                        placeholder="Choose a user."
                                        :filterResults="false"
                                        :minChars="1"
                                        :resolveOnLoad="true"
                                        :delay="0"
                                        :searchable="true"
                                        limit="50"
                                        @select="onSelectUser"
                                        :loading="true"
                                        :options="async function(query) {
                                                return await fetchUser(query)
                                              }"
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Roles</label>
                                <div class="col-sm-10">
                                    <Multiselect
                                        v-model="selectedRoles"
                                        placeholder="Choose Roles"
                                        :filterResults="true"
                                        :minChars="1"
                                        :resolveOnLoad="true"
                                        :delay="0"
                                        :searchable="true"
                                        mode="multiple"
                                        limit="50"
                                        :loading="true"
                                        :options="async function(query) {
                                                return await fetchRoles(query)
                                              }"

                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm flex justify-content-end">
                                    <input type="submit" class="btn btn-success" value="Update">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </authenticated-layout>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Multiselect from '@vueform/multiselect'

export default {
    name: "UserRole",
    props: ["users", "roles"],
    components: {
        AuthenticatedLayout,
        CardHeader,
        Actions,
        Multiselect
    },
    data() {
        return {
            selectedUser: "",
            selectedRoles: [],

        }
    },
    mounted() {},
    watch: {},
    beforeMount() {
    },
    methods: {
        async fetchUser(query){
            let where = ''

            if (query) {
                where = {'name': query}
            }
            const response = await fetch(route('users.get', where))

            const data = await response.json();
            const finalData = data.map((item) => {
                return { value: item.id, label: item.name }
            })
            return finalData;
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
        async onSelectUser(){
            const response = await fetch(route('users.roles',this.selectedUser))
            const responseJson = await response.json()
            this.selectedRoles = responseJson.map((item) => {
                return item.id;
            });
            console.log(responseJson)
        },
        submitForm(){
            const formData = {
                user: this.selectedUser,
                roles: this.selectedRoles
            }
            this.$inertia.put(route('user_role.put'), formData)
        }
    }
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>

</style>
