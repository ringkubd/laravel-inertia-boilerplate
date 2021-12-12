<template>
    <breeze-authenticated-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex mb-2">
                            <label for="only_madrasa" class="btn bg-cyan-500 shadow-lg shadow-cyan-500/50">
                                <input type="checkbox" value="1" id="only_madrasa" v-model="only_madrasa">
                                Only Madrasah
                            </label>
                            <label for="only_polytechnic" class="btn bg-indigo-500 shadow-lg shadow-indigo-500/50 ml-2">
                                <input type="checkbox" value="1" id="only_polytechnic" v-model="only_polytechnic">
                                Only Polytechnic
                            </label>
                        </div>
                        <div class="shadow flex">
                            <input class="w-full rounded p-2" type="text" placeholder="Search student..." @keyup="search" v-model="search_input">
                            <button class="w-auto flex justify-end items-center text-blue p-2 hover:text-blue-light" @click:prevent="search">
                                <i class="material-icons">search</i>
                            </button>
                        </div>

                        <div class="flex flex-wrap justify-center items-center" >
                            <div class="w-48 h-64 rounded-xl bg-gray-200 m-2 flex flex-col items-center md shadow-lg shadow-indigo-500/30" v-for="student in queryData">
                                <a :href="route('madrasa.student.show', student.id)" target="_blank">
                                    <img class="w-24 h-24 rounded-t-xl" :src="'/'+student.photo" alt="avatar" />
                                </a>
                                <div class="text-center flex flex-col p-2">
                                    <span class="text-base font-bold">
                                        <a :href="route('madrasa.student.show', student.id)" target="_blank">
                                            {{student.name}}
                                        </a>
                                    </span>
                                    <span class="text-xs text-green-700 italic">{{student?.madrasha?.name}}</span>
                                    <span class="text-xs text-red-700 italic">{{student?.polytechnic?.name}}</span>
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

export default {
    components: {
        BreezeAuthenticatedLayout,
    },
    data(){
        return {
            search_input: "",
            only_polytechnic: 0,
            only_madrasa: 0,
            queryData: []
        }
    },
    methods: {
        search(){
            let _this = this
            axios.get(route('madrasah.student.search', {'search': this.search_input, 'only_polytechnic': this.only_polytechnic, 'only_madrasa': this.only_madrasa }))
                .then((res) => {
                    _this.queryData = res.data
                    console.log(res.data)
                })
            // this.$inertia.replace(route('madrasa.student.index', {'search': query}))
        }
    }
}
</script>
