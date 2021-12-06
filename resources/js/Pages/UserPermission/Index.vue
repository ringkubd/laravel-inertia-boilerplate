<template>
    <Head>
        <title>User's Permission Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>User's Permission Management</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card flex min-h-screen mt-1">
                <div class="card-header row flex">
                    <div class="col-6 items-center">
                        <back :back-url="route('users.index')"></back>
                    </div>
                    <div class="col-6 justify-content-end">
                        <label for="roles">Users</label>
                        <select v-model="selectedUser" name="roles" id="roles" class="form-control" @change="userChange" placeholder="Select a Role">
                            <option v-for="user in users" :value="user.id">{{user.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <PermissionTable v-if="selectedUser != ''" :permissions="permissions" :selectedUser="selectedUser" :checkedPermission="updateUser" :preCheckedPermissions="preCheckedPermissions"></PermissionTable>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PermissionTable from "@/Pages/UserPermission/PermissionTable";
import Back from "@/Shared/Back";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "Index",
    components: {PageHeader, Authenticated, PermissionTable, Back},
    props: ['users', 'errors', 'user', 'permissions'],
    data() {
        return {
            selectedUser: "",
            permission: [],
            preCheckedPermissions: []
        }
    },
    methods: {
        updateUser(permissions) {
            if (this.selectedUser == "") alert("Please select a user first.");
            axios.put(route('users.direct_permission_update', this.selectedUser), {permissions: permissions}).then((res)=> {
                if (!res.data){
                    alert('Permission already assigned by role.')
                }
            })
        },
        userChange(){
            if (this.selectedRole == "") return false;
            axios.get(route('users.permission_json', this.selectedUser)).then((res) => {
                this.preCheckedPermissions = res.data;
            })
        }
    },
    mounted() {
        if(this.users.length == 1){
            this.selectedUser = this.users[0].id
            this.userChange()
        }
    }
}
</script>

<style scoped>

</style>
