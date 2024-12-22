<template>
    <Head>
        <title>Add New Application</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>Add New Application</h2>
            </PageHeader>
        </template>
        <template #default>
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body">
                    <Form
                        :submit-form="submitForm"
                        :create-form="true"
                        :errors="$page.props.errors"
                    />
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import Form from "@/Pages/Mobile/Form";
export default {
    name: "Create",
    components: {Form, PageHeader, Authenticated},
    methods:{
        submitForm(formData){
            formData.post(route('mobile.store'))
        }
    }
}
</script>

<style scoped>

</style>
