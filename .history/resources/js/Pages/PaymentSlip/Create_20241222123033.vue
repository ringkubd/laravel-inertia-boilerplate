<template>
    <Head>
        <title>Add New Payment Slip</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>Add New Payment Slip</h2>
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
import Form from "@/Pages/PaymentSlip/Form";
export default {
    name: "Create",
    components: {Form, PageHeader, Authenticated},
    methods:{
        submitForm(formData){
            formData.post(route('payment-slip.store'))
        }
    }
}
</script>

<style scoped>

</style>
