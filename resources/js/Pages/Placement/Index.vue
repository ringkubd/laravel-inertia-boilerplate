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
                <div class="card-body table-responsive relative overflow-x-auto">
                    <table  class="w-full text-sm text-left rtl:text-right text-gray-800 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 border">Sl#</th>
                            <th scope="col" class="px-6 py-3 border">Name</th>
                            <th scope="col" class="px-6 py-3 border">Session</th>
                            <th scope="col" class="px-6 py-3 border">Present Status</th>
                            <th scope="col" class="px-6 py-3 border">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" v-for="(place, index) in placements.data">
                            <td class="border text-left px-2">{{index + 1}}</td>
                            <td class="border text-left px-2">{{place.student?.name}}</td>
                            <td class="text-center border">{{place.student?.polytechnic_session}}</td>
                            <td class="border text-left px-2">{{currentStatus(place.present_status_type)}}</td>
                            <td class="text-center border"><Actions :can="can" :delete-url="route('placement.destroy', place.id)" /></td>
                        </tr>
                        </tbody>
                    </table>
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
            this.$inertia.replace(route("placement.index", { search: param }));
        },
        currentStatus(present_status_type){
            switch (present_status_type){
                case 'App\\Models\\FurtherEducation':
                    return 'Higher Study';
                case 'App\\Models\\Employment':
                    return 'Job';
                case 'App\\Models\\OtherPlacementStatus':
                    return  'Other';
            }
        }
    },
};
</script>
