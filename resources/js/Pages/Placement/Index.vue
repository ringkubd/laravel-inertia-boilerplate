<template>
    <Head>
        <title>Placement's Management</title>
    </Head>
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Placement's Management
            </h2>
        </template>
        <div class="container-fluid">
            <div class="card flex h-screen justify-center mt-1">
                <div class="card-header">
                    <card-header
                        :create="route('placement.create')"
                        :searchMethod="search"
                        :can="can"
                    ></card-header>
                </div>
                <div class="card-body table-responsive">
                    <Paginator :paginator="placements"> </Paginator>
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
    props: ["placements", 'can'],
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
