<template>
    <Head>
        <title>Product Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <page-header>Product Management</page-header>
        </template>
        <div class="container-fluid min-h-screen">
            <div class="card mt-1 min-vh-100">
                <div class="card-header">
                </div>
                <div class="card-body">
                    <Form
                        :product="product"
                        :unit="units"
                    />
                </div>
            </div>
        </div>
    </Authenticated>
</template>
<script>

import PageHeader from "@/Shared/PageHeader.vue";
import Authenticated from "@/Layouts/Authenticated.vue";
import Form from "@/Pages/Inventory/Products/Form.vue";

export default {
    name:'ProductCreat',
    props: ['can', 'product', 'units'],
    components: {Form, Authenticated, PageHeader}
}
</script>

<style scoped>

</style>
