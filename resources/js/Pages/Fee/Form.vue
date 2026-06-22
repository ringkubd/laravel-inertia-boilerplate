<template>
    <div>
        <div class="card mt-1 min-h-screen">
            <div class="card-header">
                <back :back-url="route('fee.index')"></back>
            </div>
            <div class="card-body">
                <form @submit.prevent="postData" class="max-w-5xl mx-auto">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Fee Breakdown</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="student" class="block text-sm font-medium text-gray-700 mb-1">Student's Amount</label>
                                <input type="number" id="student" class="form-control" v-model="formData.student">
                                <div v-if="errors.student" class="text-red-600 text-xs mt-1">{{ errors.student }}</div>
                            </div>
                            <div>
                                <label for="board" class="block text-sm font-medium text-gray-700 mb-1">Board Fee / Book & Stationary</label>
                                <input type="number" id="board" class="form-control" v-model="formData.board">
                                <div v-if="errors.board" class="text-red-600 text-xs mt-1">{{ errors.board }}</div>
                            </div>
                            <div>
                                <label for="institute" class="block text-sm font-medium text-gray-700 mb-1">Institute Fee</label>
                                <input type="number" id="institute" class="form-control" v-model="formData.institute">
                                <div v-if="errors.institute" class="text-red-600 text-xs mt-1">{{ errors.institute }}</div>
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                                <input disabled type="number" class="form-control bg-brand-50 text-brand-800 font-bold text-lg border-brand-200" id="amount" name="amount" v-model="amount">
                                <div v-if="errors.amount" class="text-red-600 text-xs mt-1">{{ errors.amount }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Fee Configuration</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="session" class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                                <select name="session" id="session" class="form-control" v-model="formData.academic_session">
                                    <option value="" disabled>Select Session</option>
                                    <option v-for="(ses, index) in sessions" :value="ses.session">{{ses.session}}</option>
                                </select>
                                <div v-if="errors.session" class="text-red-600 text-xs mt-1">{{ errors.session }}</div>
                            </div>
                            <div>
                                <label for="trade" class="block text-sm font-medium text-gray-700 mb-1">Trade</label>
                                <select name="trade" id="trade" class="form-control" v-model="formData.trade">
                                    <option value="all">All</option>
                                    <option v-for="(trade, index) in trades" :value="trade.name">{{trade.name}}</option>
                                </select>
                                <div v-if="errors.trade" class="text-red-600 text-xs mt-1">{{ errors.trade }}</div>
                            </div>
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                                <select name="semester" id="semester" class="form-control" v-model="formData.semester">
                                    <option value="" disabled>Select</option>
                                    <option v-for="n in 8" :value="n">{{ n }}{{ ordinal_suffix_of(n) }}</option>
                                </select>
                                <div v-if="errors.semester" class="text-red-600 text-xs mt-1">{{ errors.semester }}</div>
                            </div>
                            <div>
                                <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                <select class="form-control" id="fee_type" v-model="formData.fee_type">
                                    <option value="" disabled>Select Type</option>
                                    <option v-for="(type, index) in FeeTypes" :value="type.name">{{type.name}}</option>
                                </select>
                                <div v-if="errors.fee_type" class="text-red-600 text-xs mt-1">{{ errors.fee_type }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link :href="route('fee.index')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            Cancel
                        </Link>
                        <jet-button class="btn-success" v-if="createForm" type="submit">Add Fee</jet-button>
                        <jet-button class="btn-warning" v-if="!createForm" type="submit">Update Fee</jet-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Back from "@/Shared/Back";
import JetButton from "@/Components/Button"
import { Link } from '@inertiajs/vue3'
export default {
    name: "form",
    components: {
        Back,
        JetButton,
        Link
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
                student: this.dbValue.student !== undefined? this.dbValue.student : 0,
                board: this.dbValue.board !== undefined? this.dbValue.board : 0,
                institute: this.dbValue.institute !== undefined? this.dbValue.institute : 0,
                __token: this.$page.props.csrf
            },
            createForm: this.createForm
        }
    },
    methods: {
        postData(){
            const er = this.submitForm(this.formData)
        },
        ordinal_suffix_of(i){
            const j = i % 10, k = i % 100;
            if (j === 1 && k !== 11) return 'st';
            if (j === 2 && k !== 12) return 'nd';
            if (j === 3 && k !== 13) return 'rd';
            return 'th';
        },
    },
    computed: {
        amount(){
            this.formData.amount = this.formData.student + this.formData.board + this.formData.institute
            return this.formData.amount
        }
    }
}
</script>
