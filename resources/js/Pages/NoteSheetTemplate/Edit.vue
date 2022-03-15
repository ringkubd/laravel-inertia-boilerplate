<template>
    <Head>
        <title>Create Note Sheet Template</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                Create Note Sheet Template
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <Back :back-url="route('note_sheet_template.index')"/>
                    </div>
                    <div class="card-body">
                        <Form :fee_types="fee_types"  :note_template="note_template" :submitForm="submit" formType="update" :selected_fee_types="selected_fee_types"/>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Back from "@/Shared/Back";
import Form from './Form'
export default {
    name: "Create",
    components: {Back, PageHeader, Authenticated, Form},
    props: ['fee_types', 'note_template', 'selected_fee_types'],
    methods: {
        submit(formData){
            this.$inertia.put(route('note_sheet_template.update', this.note_template.id), formData)
        }
    }
}
</script>

<style scoped>

</style>
