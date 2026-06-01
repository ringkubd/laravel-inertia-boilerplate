<template>
    <Authenticated>
        <template #header>
            <page-header>
                User's Activities
            </page-header>
        </template>
        <div class="container-fluid px-2 sm:px-3">
            <div class="flex flex-col md:flex-row mt-1 gap-3">
                <div class="w-full md:w-1/4">
                    <div class="card min-h-[50vh] md:min-h-[80vh]">
                        <div class="card-header">
                            <h2>User List</h2>
                        </div>
                        <div class="card-body max-h-[40vh] md:max-h-[70vh] overflow-y-auto">
                            <ol>
                                <li v-for="user in users.data">
                                    <NavLink :active="route().current(`activity.${user.id}`)" :href="route('activity', user.id)">{{user.name}}</NavLink>
                                </li>
                            </ol>
                        </div>
                        <div class="card-footer">
                            <Paginator :paginator="users" />
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-3/4">
                    <div class="card min-h-[50vh] md:min-h-[80vh]">
                        <div class="card-header"></div>
                        <div class="card-body overflow-x-auto">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>SL#</th>
                                    <th>User</th>
                                    <th>Subject Type</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(activity, index) in activitiesData">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ activity?.user?.name }}</td>
                                        <td>
                                            {{ activity.subject }}
                                        </td>
                                        <td>
                                            {{ activity.description }}
                                        </td>
                                        <td>{{ activity.lapse }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import NavLink from "@/Components/NavLink";
import Paginator from "@/Components/Paginator";
export default {
    name: "Activity",
    components: {Paginator, NavLink, Authenticated},
    props: ['users', 'activities', 'active_user'],
    data() {
        return {
            activitiesData: this.activities.data
        }
    },
    created(){
        this.listenForActivity();
    },
    methods:{
        activeLink(user){
            return `activity/${user}`;
        },
        listenForActivity() {
            Echo.private('activity.' + this.active_user.id)
                .listen('ActivityLogged', (e) => {
                    console.log(e.data.description);
                    this.activitiesData.unshift(e.data);
                });
        }
    }
}
</script>

<style scoped>

</style>
