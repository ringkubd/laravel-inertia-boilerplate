<template>
    <Authenticated>
        <template #header>
            <PageHeader>Category Management</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card mt-5 min-vh-100">
                    <div class="card-header">
                        <CardHeader  :can="can" :create="route('category.create')" :index="route('category.index')" :search-method="search"></CardHeader>
                    </div>
                    <div class="card-body">
                        <table class="table table-secondary table-striped">
                            <thead>
                            <tr>
                                <th>Sl#</th>
                                <th>Title</th>
                                <th>Parent</th>
                                <th>Description</th>
                                <th>Modified</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(category, index) in categories.data">
                                <td>{{index + 1}}</td>
                                <td>{{category.title}}</td>
                                <td>
                                    <span v-if="category.parent_category">
                                        {{category.parent_category.title}}
                                    </span>
                                </td>
                                <td>{{category.description}}</td>
                                <td>{{modifiedFromNow(category.updated_at)}}</td>
                                <td>
                                    <Actions :can="can" :edit-url="route('category.edit', category.id)" :delete-url="route('category.destroy', category.id)" :detail-url="route('category.show', category.id)"></Actions>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <paginator :paginator="categories"></paginator>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>


<script>
import moment from "moment";
import Authenticated from "@/Layouts/Authenticated";
import Actions from "@/Shared/Actions";
import Paginator from "@/Components/Paginator";
import CardHeader from "@/Shared/CardHeader";
import PageHeader from "@/Shared/PageHeader";

export default {
    name: "Index",
    props: ['categories', 'can'],
    components: {PageHeader, CardHeader, Paginator, Actions, Authenticated},
    data(){
        return {

        }
    },
    methods: {
        modifiedFromNow(dateTime){
            return moment(dateTime, 'YYYY-MM-DDTh:mm:ss').fromNow()
        },
        search(param){
            this.$inertia.replace(route('category.index', {'search': param}))
        }
    }
}
</script>

<style scoped>

</style>
