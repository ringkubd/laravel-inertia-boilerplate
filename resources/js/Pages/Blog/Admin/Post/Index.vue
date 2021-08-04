<template>
    <Authenticated>
        <template #header>
            <h2>Post Management</h2>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader :create="route('post.create')" :index="route('post.index')" :search-method="search"></CardHeader>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Sl#</th>
                                <th>Title</th>
                                <th>Meta Title</th>
                                <th>Slug</th>
                                <th>Published</th>
                                <th>Summary</th>
                                <th>Content</th>
                                <th>Thumbnail</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(post, index) in posts">
                                <td>{{index + 1}}</td>
                                <td>{{post.title}}</td>
                                <td>{{post.meta_title}}</td>
                                <td>{{post.slug}}</td>
                                <td>{{post.published_at}}</td>
                                <td>{{post.summary}}</td>
                                <td>{{post.content}}</td>
                                <td>
                                    <img src="" v-if="post.thumbnail" :src="post.thumbnail" alt="">
                                </td>
                                <td>
                                    {{post.author.name}}
                                </td>
                                <td>
                                    {{post.post_status}}
                                </td>
                                <td>
                                    <Actions :edit-url="route('post.edit', post.id)" :delete-url="route('post.destroy', post.id)"></Actions>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
export default {
    name: "Index",
    props: ['user', 'posts'],
    components: {Actions, CardHeader, Authenticated},
    data(){
        return {
        }
    },
    methods: {
        search() {

        }
    }
}
</script>

<style scoped>

</style>
