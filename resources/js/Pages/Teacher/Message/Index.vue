<template>
    <Head>
        <title>Teacher's Conversation</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Teacher's Conversation</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="route('teacher.message.create')" :search-method="search">
                    </CardHeader>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-secondary">
                        <thead>
                        <tr>
                            <th>Sl.#</th>
                            <th>Conversation</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Message Count</th>
                            <th>Last Message</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(conv, index) in conversations">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ conv[0].conversation_id }}
                                </td>
                                <td>
                                    {{ conv[0].from }}
                                </td>
                                <td>
                                    {{ conv[0].to }}
                                </td>
                                <td>
                                    {{ conv.length }}
                                </td>
                                <td>
                                    {{ conv[0].created_at }}
                                </td>

                                <td>
                                    <Actions :can="can" :delete-url="route('teacher.message.destroy', conv.id)" :edit-url="route('teacher.message.edit', conv.id)"></Actions>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
import PageHeader from "@/Shared/PageHeader";
export default {
    name: "Index",
    components: {PageHeader, Paginator, Actions, CardHeader, Authenticated},
    props: ['can', 'conversations'],
    data(){
        return {
            filterParam: {
                trade: null,
                session: null
            }
        }
    },
    methods: {
        search(query){
            this.$inertia.replace(route('teacher.message.index', {'search': query}))
        },
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
