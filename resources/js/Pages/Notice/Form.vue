<template>
    <div>
        <div class="card mt-1 min-h-screen">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('notice.index')"></back>
                    </div>
                    <div class="card-body form-inline flex justify-center">
                        <form @submit.prevent="postData" class="max-w-2xl">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control" required v-model="formData.title">
                                    <div v-if="$page.props.errors.title" class="text-danger">
                                        {{ $page.props.errors.title }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="body">Body <span class="text-danger">*</span></label>
                                    <textarea  id="body" class="form-control" required v-model="formData.body"> </textarea>
                                    <div v-if="$page.props.errors.body" class="text-danger">
                                        {{ $page.props.errors.body }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="session">Session</label>
                                    <select name="session" id="session" class="form-control" v-model="formData.academic_session">
                                        <option v-for="(ses, index) in sessions" :value="ses?.session">{{ses?.session}}</option>
                                    </select>
                                    <div v-if="$page.props.errors.academic_session" class="text-danger">
                                        {{ $page.props.errors.academic_session }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="class">Class</label>
                                    <select name="class" id="class" class="form-control"  v-model="formData.class_room_id">
                                        <option v-for="(cl, index) in class_rooms" :value="cl.id">{{cl?.name}}</option>
                                    </select>
                                    <div v-if="$page.props.errors.academic_session" class="text-danger">
                                        {{ $page.props.errors.academic_session }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="published_at">Published at <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="published_at" class="form-control" required v-model="formData.published_at">
                                    <div v-if="$page.props.errors.published_at" class="text-danger">
                                        {{ $page.props.errors.published_at }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group flex justify-content-end items-end">
                                <jet-button class="btn-success" v-if="createForm" type="submit">Add</jet-button>
                                <jet-button class="btn-warning" v-if="!createForm" type="submit">Update</jet-button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Back from "@/Shared/Back";
import JetButton from "@/Components/Button"
export default {
    name: "form",
    components: {
        Back,
        JetButton
    },
    props: ['notice', 'flash', 'createForm','submitForm', 'sessions', 'class_rooms'],
    data(){
        return {
            formData: {
                class_room_id: this.notice.class_room_id,
                academic_session: this.notice.academic_session,
                body: this.notice.body,
                title: this.notice.title,
                published_at: this.notice.published_at,
                __token: this.$page.props.csrf

            },
            createForm: this.createForm
        }
    },
    methods: {
        postData(){
            const er = this.submitForm(this.formData)
        }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
