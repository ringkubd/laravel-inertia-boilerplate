<template>
    <Head>
        <title>Permission's Management</title>
    </Head>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permission's Management
            </h2>
        </template>
        <div class="container-fluid">
            <div class="card flex h-screen justify-center mt-1">
                <div class="card-header">
                    <card-header
                        :create="route('permission.create')"
                        :searchMethod="search"
                        :can="can"
                    ></card-header>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped text-center">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Guard</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(permission, index) in permissions.data"
                                :key="permission.id"
                            >
                                <td>
                                    {{ index + 1 }}
                                </td>
                                <td>
                                    {{ permission.name }}
                                </td>
                                <td>
                                    {{ permission.guard_name }}
                                </td>
                                <td>
                                    <Actions
                                        :can="can"
                                        :editUrl="
                                            route(
                                                'permission.edit',
                                                permission.id
                                            )
                                        "
                                        :deleteUrl="
                                            route(
                                                'permission.destroy',
                                                permission.id
                                            )
                                        "
                                    ></Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Paginator :paginator="permissions"> </Paginator>
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
    props: ["permissions", 'can'],
    components: {
        BreezeAuthenticatedLayout,
        CardHeader,
        Actions,
        Paginator,
    },
    methods: {
        search(param) {
            this.$inertia.replace(route("permission.index", { search: param }));
        },
    },
};
</script>
