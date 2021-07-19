<template>
<Authenticated>
    <template #header>
        <h2>Role's Permission Management</h2>
    </template>
    <div class="container-fluid">
        <div class="card flex h-screen justify-center mt-5">
            <div class="card-header row flex">
                <div class="col-6 items-center">
                   <back :back-url="route('roles.index')"></back>
                </div>
                <div class="col-6 justify-content-end">
                    <label for="roles">Roles</label>
                    <select v-model="selectedRole" name="roles" id="roles" class="form-control" @change="roleChange" placeholder="Select a Role">
                        <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <PermissionTable v-if="selectedRole != ''" :permissions="permissions" :selectedRole="selectedRole" :checkedPermission="updateRole" :preCheckedPermissions="preCheckedPermissions"></PermissionTable>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PermissionTable from "@/Pages/RolePermission/PermissionTable";
import Back from "@/Shared/Back";
export default {
name: "Index",
    components: {Back, Authenticated, PermissionTable},
    props: ['roles', 'errors', 'user', 'permissions'],
    data() {
        return {
            selectedRole: "",
            permission: [],
            preCheckedPermissions: []
        }
    },
    methods: {
        updateRole(permissions) {
            if (this.selectedRole == "") alert("Please select a role first.");
            axios.put(route('roles.permissions_update', this.selectedRole), {permissions: permissions})
        },
        roleChange(){
            if (this.selectedRole == "") return false;
            axios.get(route('roles.get_permissions', this.selectedRole)).then((res) => {
                this.preCheckedPermissions = res.data;
            })
        }
    },
    watch: {

    },
    mounted() {
        if(this.roles.length == 1){
            this.selectedRole = this.roles[0].id
            this.roleChange()
        }
    }
}
</script>

<style scoped>

</style>
