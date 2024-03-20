<template>
    <div>
        <div class="card mt-1 min-h-screen">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('placement.index')"></back>
                    </div>
                    <div class="card-body flex justify-center w-full">
                        <form @submit.prevent="placement" class="w-full sm:max-w-7xl">
                            <div class="row mb-4">
                                <div class="col shadow-lg py-2">
                                    <label for="name">Name</label>
                                    <select2
                                        :options="academic_sessions"
                                        v-model="formData.academic_session"
                                        class="w-full py-1.5"
                                    />
                                    <div v-if="errors.name" class="text-danger">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="col shadow-lg py-2">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" v-model="formData.email">
                                    <div v-if="errors.email" class="text-danger">
                                        {{ errors.email }}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Back from "@/Shared/Back.vue";
export default {
    name: "add",
    components: {Back},
    props: ['placement', 'flash', 'academic_sessions'],
    data(){
        return {
            formData: {
                academic_session: ''
            },
            errors: this.$page.props.errors,
        };
    },
    methods: {
        submit(formData){
            this.$inertia.post('/placement', formData)
        }
    },
    mounted() {
        console.log(this.academic_sessions)
    },
    updated() {
        console.log(this.formData)
    }
}
</script>

<style scoped>

</style>
