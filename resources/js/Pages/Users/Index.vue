<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User's Management
            </h2>
        </template>
        <div class="container-fluid">
            <div class="card flex h-screen justify-center mt-5">
                <div class="card-header">
                    <card-header
                        :create="route('users.create')"
                        :searchMethod="search"
                    ></card-header>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(user, index) in users.data"
                                :key="user.id"
                            >
                                <td>
                                    {{ index + 1 }}
                                </td>
                                <td>
                                    {{ user.name }}
                                </td>
                                <td>
                                    {{ user.email }}
                                </td>
                                <td class="flex justify-center">
                                    <Actions
                                        :editUrl="route('users.edit', user.id)"
                                        :deleteUrl="
                                            route('users.destroy', user.id)
                                        "
                                    ></Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Paginator :paginator="users"> </Paginator>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";

export default {
    name: "index",
    props: ["users"],
    components: {
        BreezeAuthenticatedLayout,
        CardHeader,
        Actions,
        Paginator,
    },
    methods: {
        search(param) {
            this.$inertia.replace(route("users.index", { search: param }));
        },
    },
};
</script>
