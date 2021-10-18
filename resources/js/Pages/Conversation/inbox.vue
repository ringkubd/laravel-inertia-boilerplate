<template>
<Authenticated>
    <div class="row h-20 bg-green-100">
        <div class="flex text-center align-text-middle">
            <div class="flex-1">Active User</div>
            <div class="flex-1">Active Information</div>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <ol>
                <li v-for="friend in friends">
                    <friend class="block">
                        <a class="no-underline block" :href="route('inbox')+'#'+friend.conversation[0].id">{{ friend.name }}</a>
                    </friend>
                </li>
            </ol>
        </div>
        <div class="col-9">

        </div>
    </div>
</Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import friend  from "./inc/friend"
export default {
    name: "inbox",
    components: {
        Authenticated,
        friend
    },
    props: ['errors', 'user', 'friends'],
    methods: {
        activeUser(){

        }
    }
}
</script>

<style scoped>

</style>
