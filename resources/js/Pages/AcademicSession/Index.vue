<template>
    <Head>
        <title>Academic Session Management</title>
    </Head>
    <app-layout>
        <template #header>
            <page-header>Academic Session</page-header>
        </template>
        <div class="container-fluid">
            <div class="card mt-5 min-vh-100">
                <div class="card-header">
                    <CardHeader :can="can" :create="false" :search-method="search">
                        <template #first>
                            <button id="show-modal" @click="showModal = true">
                                <font-awesome-icon
                                    icon="plus"
                                    size="md"
                                    rotation="rotate"
                                ></font-awesome-icon>
                                Add New
                            </button>
                        </template>
                    </CardHeader>
                    <transition name="modal">
                        <Modal :show-modal="showModal" v-if="showModal" @close="showModal = false">
                            <template v-slot:header>
                                <h3>New Session Form</h3>
                            </template>
                            <template #body>
                                <form action=""  @submit.prevent="formSubmit">
                                    <div class="form-group">
                                        <label for="session">Session<span class="text-red-500"><super>*</super></span></label>
                                        <input v-model="form.academic_session" required type="text" placeholder="2020-2021" class="form-control" id="session" />
                                        <InputError  :message="errors.academic_session"/>
                                    </div>
                                    <div class="form-group mt-4 flex justify-end">
                                        <div>
                                            <input type="submit" class="btn btn-primary btn-lg" value="Add">
                                        </div>
                                    </div>
                                </form>
                            </template>
                        </Modal>
                    </transition>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-secondary text-center">
                        <thead>
                        <tr>
                            <th scope="col">Sl#</th>
                            <th scope="col">SESSION</th>
                            <th scope="col">ACTIONS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(session, index) in data.data">
                            <td>{{ index + 1 }}</td>
                            <td>{{ session.session }}</td>
                            <td>
                                <Actions :can="can" :delete-url="route('academic_session.destroy', session.id)" :editUrl="route('academic_session.edit', session.id)"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <Paginator :paginator="data"/>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Authenticated";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
import '@jobinsjp/vue3-datatable/dist/style.css'
import PageHeader from "@/Shared/PageHeader";
import Modal from './Modal'
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import InputError from "@/Components/InputError";
import Paginator from "@/Components/Paginator";
library.add(faPlus);

export default {
    components: {
        Paginator,
        InputError,
        PageHeader,
        Actions,
        CardHeader,
        AppLayout,
        Modal
    },
    props: ['data', 'errors', 'flash', 'can'],
    data(){
        return {
            form: {
                academic_session : null,
            },
            showModal: false
        }
    },
    updated(){
        console.log(this.errors)
    },
    methods: {
        search(searchItem){
            this.$inertia.replace(route('academic_session.index', {'search': searchItem}))
        },
        formSubmit(e){
            e.preventDefault();
            let submit = this.$inertia.post(route('academic_session.store'), this.form)
            this.form.academic_session = ""
            console.log(this.errors)
        }
    },
}
</script>

<style scoped>

</style>
