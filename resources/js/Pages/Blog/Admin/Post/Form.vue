<template>
    <div class="flex h-screen justify-center mt-5">
        <form action="" @submit.prevent="submit()">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="inputPassword6" class="col-form-label">Password</label>
                </div>
                <div class="col-auto">
                    <input type="password" id="inputPassword6" class="form-control" aria-describedby="passwordHelpInline">
                </div>
                <div class="col-auto">
                    <span id="passwordHelpInline" class="form-text">
                      Must be 8-20 characters long.
                    </span>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: "Form",
    props: [],
    data(){
        return {
        }
    },
    methods: {
        submit(){

        }
    }

}
</script>

<style scoped>

</style>
