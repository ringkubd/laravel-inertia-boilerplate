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
                                <th>Slug</th>
                                <th>Categories</th>
                                <th>Tags</th>
                                <th>Thumbnail</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Modified</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(post, index) in posts.data">
                                <td>{{index + 1}}</td>
                                <td>{{post.title}}</td>
                                <td>{{post.slug}}</td>
                                <td v-html="categories(post.categories)"></td>
                                <td v-html="tags(post.tags)"></td>
                                <td>
                                    <img v-if="post.thumbnail" :src="post.thumbnail"  width="50" :alt="post.title">
                                </td>
                                <td>
                                    {{post.author.name}}
                                </td>
                                <td>
                                    {{post.post_status}}
                                </td>
                                <td>{{post.updated_at}}</td>
                                <td>
                                    <Actions :edit-url="route('post.edit', post.id)" :delete-url="route('post.destroy', post.id)"></Actions>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <paginator :paginator="posts"></paginator>
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
import Paginator from "@/Components/Paginator";
export default {
    name: "Index",
    props: ['user', 'posts'],
    components: {Paginator, Actions, CardHeader, Authenticated},
    data(){
        return {

        }
    },
    methods: {
        search(param) {
            this.$inertia.replace(route('post.index', {'search': param}))
        },
        categories(categories) {
            let categoryLink = ""
            categories.forEach((value) => {
                let link = "<a href='"+route('category.show', value.id) +"'>" + value.title + "</a>,"
                categoryLink += link
            })
            return categoryLink;
        },
        tags(tags) {
            let tagLink = ""
            tags.forEach((value) => {
                let link = "<a href='"+route('tag.show', value.id) +"'>" + value.title + "</a>,"
                tagLink += link
            })
            return tagLink;
        }
    }
}
</script>

<style scoped>

</style>
