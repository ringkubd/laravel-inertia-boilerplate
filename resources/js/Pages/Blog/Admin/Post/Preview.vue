<template>
    <authenticated>
        <template #header>
            {{post.title}}
        </template>
        <template #default>
            <div  class="bg-white font-sans leading-normal tracking-normal">
                <!--Title-->
                <div class="text-center pt-16 md:pt-32">
                    <p class="text-sm md:text-base text-green-500 font-bold">{{modifiedFromNow(post.updated_at)}} <span class="text-gray-900">/</span> <span v-html="categories(post.categories)"></span></p>
                    <h1 class="font-bold break-normal text-3xl md:text-5xl">{{post.title}}</h1>
                </div>

                <!--image-->
                <div class="container w-full max-w-6xl mx-auto bg-white bg-cover mt-8 rounded" :style="styleObject"></div>

                <!--Container-->
                <div class="container max-w-5xl mx-auto -mt-32">

                    <div class="mx-0 sm:mx-6">
                        <!-- Post Content-->
                        <div class="bg-white w-full p-8 md:p-24 text-xl md:text-2xl text-gray-800 leading-normal" style="font-family:Georgia,serif;" v-html="post.content"></div>
                        <!-- End Post Content-->


                        <!--Subscribe-->
                        <div class="container font-sans bg-green-100 rounded mt-8 p-4 md:p-24 text-center" v-if="subscribe">
                            <h2 class="font-bold break-normal text-2xl md:text-4xl">Subscribe to Ghostwind CSS</h2>
                            <h3 class="font-bold break-normal font-normal text-gray-600 text-base md:text-xl">Get the latest posts delivered right to your inbox</h3>
                            <div class="w-full text-center pt-4">
                                <form action="#">
                                    <div class="max-w-sm mx-auto p-1 pr-0 flex flex-wrap items-center">
                                        <input type="email" placeholder="youremail@example.com" class="flex-1 appearance-none rounded shadow p-3 text-gray-600 mr-2 focus:outline-none">
                                        <button type="submit" class="flex-1 mt-4 md:mt-0 block md:inline-block appearance-none bg-green-500 text-white text-base font-semibold tracking-wider uppercase py-4 rounded shadow hover:bg-green-400">Subscribe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Subscribe-->


                        <!--Author-->
                        <div class="flex w-full items-center font-sans p-8 md:p-24">
                            <img class="w-10 h-10 rounded-full mr-4" v-if="post.profile_photo" :src="post.profile_photo" alt="Avatar of Author">
                            <div class="flex-1">
                                <p class="text-base font-bold text-base md:text-xl leading-none">{{post.author.name}}</p>
                                <p class="text-gray-600 text-xs md:text-base">{{post.author.about_me}} <a class="text-gray-800 hover:text-green-500 no-underline border-b-2 border-green-500" :href="post.author.portfolio_link">{{post.author.title}}</a></p>
                            </div>
                            <div class="justify-end" v-if="false">
                                <button class="bg-transparent border border-gray-500 hover:border-green-500 text-xs text-gray-500 hover:text-green-500 font-bold py-2 px-4 rounded-full">Read More</button>
                            </div>
                        </div>
                        <!--/Author-->

                    </div>


                </div>
                <div class="bg-gray-200">
                    <div class="container w-full max-w-6xl mx-auto px-2 py-8">
                        <div class="flex flex-wrap -mx-2">
                            <div class="w-full md:w-1/3 px-2 pb-12" v-for="post in related_post">
                                <div class="h-full bg-white rounded overflow-hidden shadow-md hover:shadow-lg relative smooth">
                                    <a :href="route('post.show', post.slug)" class="no-underline hover:no-underline">
                                        <img :src="post.thumbnail" class="h-48 w-full rounded-t shadow-lg">
                                        <div class="p-6 h-auto md:h-48">
                                            <p class="text-gray-600 text-xs md:text-sm">{{post.author.name}}</p>
                                            <div class="font-bold text-xl text-gray-900">{{post.title}}</div>
                                            <p class="text-gray-800 font-serif text-base mb-5" v-html="post.content">
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between inset-x-0 bottom-0 p-6">
                                            <img class="w-8 h-8 rounded-full mr-4" v-if="post.profile_img" :src="post.profile_img" alt="Avatar of Author">
                                            <p class="text-gray-600 text-xs md:text-sm">{{modifiedFromNow(post.updated_at)}} </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <footer class="bg-gray-900">
                    <div class="container max-w-6xl mx-auto flex items-center px-2 py-8">

                        <div class="w-full mx-auto flex flex-wrap items-center">
                            <div class="flex w-full md:w-1/2 justify-center md:justify-start text-white font-extrabold">
                                <a class="text-gray-900 no-underline hover:text-gray-900 hover:no-underline" href="#">
                                    <span class="text-base text-gray-200">Ghostwind CSS</span>
                                </a>
                            </div>
                            <div class="flex w-full pt-2 content-center justify-between md:w-1/2 md:justify-end">
                                <ul class="list-reset flex justify-center flex-1 md:flex-none items-center">
                                    <li>
                                        <a class="inline-block py-2 px-3 text-white no-underline" href="index.html">HOME</a>
                                    </li>
                                    <li>
                                        <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                                    </li>
                                    <li>
                                        <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                                    </li>
                                    <li>
                                        <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                                    </li>
                                </ul>
                            </div>
                        </div>



                    </div>
                </footer>
            </div>
        </template>
    </authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import moment from "moment";
export default {
    name: "Preview",
    components: {Authenticated},
    props: ['post', 'user', 'related_post'],
    data() {
        return {
            styleObject: {
               backgroundImage:'url('+ this.post.thumbnail +")",
                height: "75vh"
            },
            subscribe: false,
        }
    },
    mounted(){

    },
    methods: {
        modifiedFromNow(dateTime){
            return moment(dateTime, 'YYYY-MM-DDTh:mm:ss').fromNow()
        },
        categories(categories) {
            let categoryLink = ""
            categories.forEach((value) => {
                let link = "<a href='"+route('category.show', value.id) +"'>" + value.title + "</a>/"
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
        },
        relatedPost(){


        }
    }
}
</script>

<style scoped>

</style>
