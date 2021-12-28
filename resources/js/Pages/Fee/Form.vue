<template>
    <div>
        <div class="card mt-1 min-h-screen">
            <div class="row">
                <div class="col-12">
                    <div class="card-header">
                        <back :back-url="route('fee.index')"></back>
                    </div>
                    <div class="card-body form-inline">
                        <form @submit.prevent="postData">
                            <div class="row mb-4">
                                <div class="col">
                                    <label for="session">Session</label>
                                    <select name="session" id="session" class="form-control" v-model="formData.academic_session">
                                        <option v-for="(ses, index) in sessions" :value="ses.session">{{ses.session}}</option>
                                    </select>
                                    <div v-if="errors.session" class="text-danger">
                                        {{ errors.session }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="trade">Trade</label>
                                    <select name="trade" id="trade" class="form-control"  v-model="formData.trade">
                                        <option value="all">All</option>
                                        <option v-for="(trade, index) in trades" :value="trade.name">{{trade.name}}</option>
                                    </select>
                                    <div v-if="errors.trade" class="text-danger">
                                        {{ errors.trade }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md">
                                    <label for="semester">Semester</label>
                                    <select name="semester" id="semester" class="form-control" v-model="formData.semester">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                    </select>
                                    <div v-if="errors.semester" class="text-danger">
                                        {{ errors.semester }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="fee_type">Fee Type</label>
                                    <select class="form-control" id="fee_type" v-model="formData.fee_type">
                                        <option v-for="(type, index) in FeeTypes" :value="type.name">{{type.name}}</option>
                                    </select>
                                    <div v-if="errors.fee_type" class="text-danger">
                                        {{ errors.fee_type }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" v-model="formData.amount">
                                    <div v-if="errors.amount" class="text-danger">
                                        {{ errors.amount }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
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
    props: ['dbValue', 'flash', 'createForm','submitForm', 'sessions', 'trades', 'FeeTypes', 'errors'],
    data(){
        return {
            formData: {
                academic_session: this.dbValue.session,
                trade: this.dbValue.trade,
                semester: this.dbValue.semester,
                fee_type: this.dbValue.fee_type,
                amount: this.dbValue.amount,
                __token: this.$page.props.csrf

            },
            createForm: this.createForm
        }
    },
    methods: {
        postData(){
            const er = this.submitForm(this.formData)
        }
    }
}
</script>

<style scoped>

</style>
