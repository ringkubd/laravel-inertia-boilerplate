<template>
    <table class="table table-bordered">
        <tbody>
        <tr v-for="(permission, module) in permissions">
            <th>{{capitalizeFirstLetter(module)}}</th>
            <td v-for="p in permission">
                <input @change="selectPermission(p.id)" :checked="checked(p.id)" type="checkbox" :value="p.id" class="mr-2 check-input">{{capitalizeFirstLetter(splitConcat(p.name))}}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "PermissionTable",
    props: ['permissions', 'checkedPermission', 'selectedUser', 'preCheckedPermissions'],
    data(){
        return {
            formData: {
                permission: ''
            }
        }
    },
    methods: {
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        splitConcat(string){
            const str = string.split("_");
            return str.join(" ")
        },
        selectPermission(permissionId){
            this.checkedPermission(permissionId)
        },
        checked(permissionId){
            if (this.preCheckedPermissions.includes(permissionId)){
                return true;
            }
            return false;
        }
    }
}
</script>

<style scoped>

</style>
