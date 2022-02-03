<template>
    <Head>
        <title>Project Configuration File Editor</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Create new configuration file.</PageHeader>
        </template>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <Back :back-url="route('config.index')" />
                </div>
                <div class="card-body">
                    <Form />
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Back from "@/Shared/Back";
import PageHeader from "@/Shared/PageHeader";
import Form from "@/Pages/Settings/Form";
export default {
    name: "Create",
    components: {Form, PageHeader, Back, Authenticated}
}
</script>

<style scoped>

</style>
