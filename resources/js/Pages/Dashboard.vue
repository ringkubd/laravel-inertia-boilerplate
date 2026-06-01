<template>
    <Head>
        <title>Dashboard IsDB-BISEW</title>
    </Head>
    <breeze-authenticated-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <label for="only_madrasa" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-brand-100 text-brand-800 cursor-pointer hover:bg-brand-200 transition">
                                <input type="checkbox" value="1" id="only_madrasa" v-model="only_madrasa" @change="search" class="rounded border-gray-300 text-brand-600">
                                Only Madrasah
                            </label>
                            <label for="only_polytechnic" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-accent-100 text-accent-800 cursor-pointer hover:bg-accent-200 transition">
                                <input type="checkbox" value="1" id="only_polytechnic" v-model="only_polytechnic" @change="search" class="rounded border-gray-300 text-accent-600">
                                Only Polytechnic
                            </label>
                        </div>
                        <div class="relative mb-6">
                            <input class="w-full rounded-lg border border-gray-300 pl-4 pr-12 py-2.5 text-sm focus:border-brand-300 focus:ring focus:ring-brand-200 focus:ring-opacity-50" type="text" placeholder="Search student..." @keyup="search" v-model="search_input">
                            <button class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-brand-600" @click.prevent="search">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap justify-center items-center" >
                            <h2 v-if="empty_queryData" class="my-2 text-gray-500">
                                Your search - <b><i>{{ search_input }}</i></b> - did not match in our student list.
                            </h2>
                            <div class="w-36 sm:w-48 h-64 rounded-xl bg-white border border-gray-200 sm:m-2 m-1 flex flex-col items-center shadow-sm hover:shadow-md transition-shadow" v-for="student in queryData.data">
                                <a :href="route('madrasa.student.show', student.id)" target="_blank" class="pt-3">
                                    <img v-if="student.photo" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-gray-100" :src="'/'+student.photo" alt="avatar" />
                                </a>
                                <div class="text-center flex flex-col p-2 flex-1 justify-center">
                                    <span class="text-sm font-bold text-gray-900">
                                        <a :href="route('madrasa.student.show', student.id)" target="_blank" class="hover:text-brand-600">
                                            {{student.name}}
                                        </a>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{student?.classroom[0]?.name}}
                                    </span>
                                    <span class="text-xs text-accent-600 italic">{{student?.madrasha?.name}}</span>
                                    <span class="text-xs text-brand-600 italic">{{student?.polytechnic_info?.name}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated'
import Paginator from "@/Components/Paginator";

export default {
    components: {
        Paginator,
        BreezeAuthenticatedLayout
    },
    data(){
        return {
            search_input: "",
            only_polytechnic: 0,
            only_madrasa: 0,
            queryData: [],
            empty_queryData: false,
            page: 1
        }
    },
    methods: {
        search(){
            let _this = this
            if(this.search_input.length <= 0) {
                _this.queryData = []
                return
            }
            axios.get(route('madrasah.student.search', {'search': this.search_input, 'only_polytechnic': this.only_polytechnic, 'only_madrasa': this.only_madrasa }))
                .then((res) => {
                    _this.queryData = res.data
                    _this.empty_queryData = false
                    if (res.data.data.length <= 0){
                        _this.empty_queryData = true
                    }
                })
        }
    },
    watch: {
        only_polytechnic(){
            if (this.only_madrasa){
                this.only_madrasa = false
            }
            return this.only_polytechnic;
        },
        only_madrasa(){
            if (this.only_polytechnic){
                this.only_polytechnic = false
            }
            return this.only_madrasa;
        }
    }
}
</script>
