<template>
    <guest>
        <template #default>
            <div  class="bg-white font-sans leading-normal tracking-normal">
                <!--Title-->
                <div class="text-center pt-16 md:pt-32">
                    <p class="text-sm md:text-base text-green-500 font-bold">{{modifiedFromNow(category.updated_at)}} <span class="text-gray-900">/</span></p>
                    <h1 class="font-bold break-normal text-3xl md:text-5xl">{{category.title}}</h1>
                </div>

                <!--image-->

                <!--Container-->
                <div class="container max-w-5xl mx-auto mt-1">

                    <div class="mx-0 sm:mx-6">
                        <!-- category Content-->
                        <div class="bg-white w-full p-8 md:p-24 text-xl md:text-2xl text-gray-800 leading-normal" style="font-family:Georgia,serif;" v-html="category.description"></div>
                        <!-- End category Content-->


                        <!--Subscribe-->
                        <div class="container font-sans bg-green-100 rounded mt-8 p-4 md:p-24 text-center" v-if="subscribe">
                            <h2 class="font-bold break-normal text-2xl md:text-4xl">Subscribe to Ghostwind CSS</h2>
                            <h3 class="font-bold break-normal font-normal text-gray-600 text-base md:text-xl">Get the latest categorys delivered right to your inbox</h3>
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
                    </div>


                </div>
                <div class="bg-gray-200">
                    <div class="container w-full max-w-6xl mx-auto px-2 py-8">
                        <div class="flex flex-wrap -mx-2">
                            <div class="w-full md:w-1/3 px-2 pb-12" v-for="post in category_post">
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
            </div>
        </template>
    </guest>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import moment from "moment";
import Guest from "@/Layouts/Guest";
export default {
    name: "Preview",
    components: {Guest, Authenticated},
    props: ['category', 'user', 'category_post'],
    data() {
        return {
            subscribe: false,
        }
    },
    mounted(){

    },
    methods: {
        modifiedFromNow(dateTime){
            return moment(dateTime, 'YYYY-MM-DDTh:mm:ss').fromNow()
        },
        relatedPost(){


        }
    }
}
</script>

<style scoped>

</style>
