<template>
    <div>
        <div class="card mt-1">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('madrasa.result.index')"></back>
                    </div>
                    <div class="card-body form-inline">
                        <form action="" @submit.prevent="postData">
                            <div class="row mb-4">
                                <div class="col" v-if="createForm">
                                    <label for="student_id">Student</label>
                                    <Multiselect
                                        id="student_id"
                                        v-model="form.student_id"
                                        :filterResults="true"
                                        :minChars="1"
                                        :delay="0"
                                        :searchable="true"
                                        mode="single"
                                        :options="async function(query) {
                                                    return await selectChangeEvent(query)
                                                }"
                                    ></Multiselect>
                                    <InputError :message="errors.student_id"/>
                                </div>
                                <div class="col">
                                    <label for="nine_gpa">Nine GPA</label>
                                    <input type="number" id="nine_gpa" step="0.01" max="5" min="1" class="form-control" v-model="form.nine_gpa">
                                    <InputError :message="errors.nine_gpa"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md">
                                    <label for="ten_gpa">Dakhil CGPA</label>
                                    <input type="number" id="ten_gpa" step="0.01" max="5" min="1"  class="form-control" v-model="form.ten_gpa">
                                    <InputError :message="errors.ten_gpa"/>
                                </div>
                                <div class="col-md">
                                    <label for="pass_year">Pass Year</label>
                                    <input type="number" id="pass_year" step="1" min="2000"  class="form-control" v-model="form.pass_year">
                                    <InputError :message="errors.pass_year"/>
                                </div>
                                <div class="col">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" v-model="form.status">
                                        <option value="Pass">Pass</option>
                                        <option value="Fail">Fail</option>
                                    </select>
                                    <InputError :message="errors.status"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <jet-button v-if="createForm" type="submit">Add</jet-button>
                                <jet-button v-if="!createForm" type="submit">Update</jet-button>
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
import Multiselect from '@vueform/multiselect'
import InputError from "@/Components/InputError";
export default {
    name: "form",
    components: {
        InputError,
        Back,
        JetButton,
        Multiselect
    },
    props: ['dbValue', 'flash', 'createForm','submitForm', 'students', 'errors'],
    data(){
        return {
            form: {
                student_id: this.dbValue.student_id,
                nine_gpa: this.dbValue.nine_gpa,
                ten_gpa: this.dbValue.ten_gpa,
                pass_year: this.dbValue.pass_year,
                status: this.dbValue.status,
            },
            createForm: this.createForm
        }
    },
    methods: {
        onloadForm(){

        },
        postData(){
            const er = this.submitForm(this.form)
        },
        async selectChangeEvent(query){
            let where = {}
            let session = GET('academic_session')
            if (query) {
                where = {'name': query}
            }
            if(session.length > 0){
                where['academic_session'] = session[0]
            }
            const response = await fetch(route('madrasa.student_list', where))
            const data = await response.json();
            return data;
        }
    },
    mounted() {
        console.log(this.errors)
    },
    updated() {
    },
    beforeMount() {

    }
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

<style scoped>

</style>
