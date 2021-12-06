<template>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role's Management
            </h2>
        </template>
        <div class="container-fluid">
            <div class="card flex h-screen justify-center mt-1">
                <div class="card-header">
                    <card-header
                        :create="route('roles.create')"
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
                                v-for="(role, index) in roles.data"
                                :key="role.id"
                            >
                                <td>
                                    {{ index + 1 }}
                                </td>
                                <td>
                                    {{ role.name }}
                                </td>
                                <td>
                                    {{ role.guard_name }}
                                </td>
                                <td>
                                    <Actions
                                        :editUrl="route('roles.edit', role.id)"
                                        :deleteUrl="
                                            route('roles.destroy', role.id)
                                        "
                                        :can="can"
                                    >
                                        <inertia-link as="button" type="button" :href="route('roles.permissions', role.id)">
                                            <jet-button type="submit" class="btn-secondary">
                                                <font-awesome-icon
                                                    icon="bars"
                                                    size="md"
                                                    rotation="rotate"
                                                >
                                                </font-awesome-icon>
                                                Permissions
                                            </jet-button>
                                        </inertia-link>
                                    </Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Paginator :paginator="roles"> </Paginator>
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
import JetButton from "@/Shared/Button";

import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen,  faBars } from "@fortawesome/free-solid-svg-icons";
library.add(faPen,faBars);


export default {
    name: "index",
    props: ["roles", 'can'],
    components: {
        BreezeAuthenticatedLayout,
        CardHeader,
        Actions,
        Paginator,
        JetButton
    },
    methods: {
        search(param) {
            this.$inertia.replace(route("roles.index", { search: param }));
        },
    },
};
</script>
