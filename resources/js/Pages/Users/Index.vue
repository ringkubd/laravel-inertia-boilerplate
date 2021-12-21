<template>
    <Head>
        <title>User's Management</title>
    </Head>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User's Management
            </h2>
        </template>
        <div class="container-fluid">
            <div class="card flex h-screen justify-center mt-1">
                <div class="card-header">
                    <card-header
                        :create="route('users.create')"
                        :searchMethod="search"
                        :can="can"
                    >
                        <template #second>
                            <inertia-link as="button" type="button" :href="route('user_role.index')">
                                <jet-button type="submit" class="btn-primary">
                                    <font-awesome-icon
                                        icon="bars"
                                        size="md"
                                        rotation="rotate"
                                        class="mr-1"
                                    >
                                    </font-awesome-icon>
                                    User Roles
                                </jet-button>
                            </inertia-link>
                        </template>
                    </card-header>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-secondary table-striped">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Madrasah</th>
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
                            <td>
                                <ol class="list-group">
                                    <li class="list-inline-item" v-for="role in user.roles">{{role.name}}</li>
                                </ol>
                            </td>
                            <td>
                                {{ user?.madrasah?.name }}
                            </td>
                            <td class="flex justify-center">
                                <Actions
                                    :can="can"
                                    :editUrl="route('users.edit', user.id)"
                                    class="text-black"
                                    :deleteUrl="
                                            route('users.destroy', user.id)
                                        "
                                >
                                    <inertia-link as="button" class="group" type="button" :href="route('users.direct_permission', user.id)">
                                        <jet-button type="submit" class="bg-gradient-to-r text-black from-[#36AFAD] via-black-700 to-[#36C57F] animate-gradient-xy hover:shadow-lg group-hover:animate-pulse">
                                            <font-awesome-icon
                                                icon="bars"
                                                size="md"
                                                rotation="rotate"
                                                class="text-black"
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
import JetButton from "@/Shared/Button";

import { library } from "@fortawesome/fontawesome-svg-core";
import { faPen,  faBars } from "@fortawesome/free-solid-svg-icons";
library.add(faPen,faBars);

export default {
    name: "index",
    props: ["users", 'can'],
    data() {
        return {
        }
    },
    components: {
        BreezeAuthenticatedLayout,
        CardHeader,
        Actions,
        Paginator,
        JetButton
    },
    methods: {
        search(param) {
            this.$inertia.replace(route("users.index", { search: param }));
        }
    },
    computed:{
    }
};
</script>
