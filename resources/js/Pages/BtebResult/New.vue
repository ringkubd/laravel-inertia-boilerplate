<template>
    <Head>
        <title>Check Result</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>Search Result</h2>
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <Back :back-url="route('bteb.index')" />
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-row justify-center items-center">
                            <form action="" @submit.prevent="search">
                                <div class="form-group">
                                    <label for="exam_type">Exam Type</label>
                                    <select name="exam" id="exam_type" v-model="exam_type" class="form-control">
                                        <option value="">--Click here to Select--</option>
                                        <option value="15">DIPLOMA IN ENGINEERING</option>
                                        <option value="19">DIPLOMA IN TEXTILE</option>
                                        <option value="21">DIPLOMA IN TEXTILE(Jute)</option>
                                        <option value="49">DIPLOMA IN TEXTILE(Germents)</option>
                                        <option value="23">DIPLOMA IN AGRICULTURE</option>
                                        <option value="23">DIPLOMA IN AGRICULTURE</option>
                                        <option value="71">DIPLOMA IN FISHERIES_IN_SERVICE</option>
                                        <option value="74">DIPLOMA IN FISHERIES</option>
                                        <option value="20">DIPLOMA IN FORESTRY</option>
                                        <option value="30">BASIC TRADE 360 HOURS</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exam_year">Exam Year</label>
                                    <select name="exam_year" id="exam_year" v-model="exam_year" class="form-control">
                                        <option v-for="year in valid_year" :value="year">{{year}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="roll">Roll</label>
                                    <input type="number" id='roll' v-model="roll" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="reg">Reg. (optional)</label>
                                    <input type="number" id='reg' v-model="reg" class="form-control">
                                </div>
                                <div class="form-group my-4">
                                    <input type="submit" class="btn btn-success" value="Search Result">
                                </div>
                            </form>
                        </div>
                        <div class="flex flex-row justify-center items-center" v-html="result"></div>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Back from "@/Shared/Back";
export default {
    name: "New",
    components: {Back, CardHeader, PageHeader, Authenticated},
    props: ['valid_year'],
    data() {
        return {
            roll: '',
            reg: '',
            exam_type: '',
            exam_year: '',
            result: ''
        }
    },
    methods: {
        search(){
            const app =  this
            axios.post(route('bteb.search'),{
                roll: this.roll,
                reg: this.reg,
                exam: this.exam_type,
                year: this.exam_year,
            })
                .then(function(res){
                    console.log(res)
                    app.result = res.data
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
}
</script>

<style scoped>

</style>
