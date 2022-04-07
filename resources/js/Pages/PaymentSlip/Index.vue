<template>
    <Head>
        <title>Payment Slip Management</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>
                <h2>Payment Slip Management</h2>
            </PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader
                            :create="route('payment-slip.create')"
                            :can="can"
                        />
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-secondary table-striped text-center">
                            <thead>
                            <tr>
                                <th>SL#</th>
                                <th>Name</th>
                                <th>Semester</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Attachment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(slip, index) in payment_slip">
                                <td>{{ index + 1 }}</td>
                                <td>{{ slip.student?.name }}</td>
                                <td>{{ slip.semester }} Semester</td>
                                <td>{{ slip.fee_type }}</td>
                                <td>{{ slip.amount }}</td>
                                <td>
                                    <vue-picture-swipe v-if="slip.attachments?.length !== 0" :items="imageItems(slip.attachments)" />
                                    <p class="p-0 m-0 text-red-700" v-else>N</p>
                                </td>
                                <td>
                                    <select
                                        @change="statusChange"
                                        :slipId="slip.id"
                                        name="status"
                                        :value="slip.status"
                                        class="rounded p-1" :class="statusStyle(slip.status)"
                                    >
                                        <option value="0">Pending</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Rejected</option>
                                    </select>
                                </td>
                                <td>
                                    <Actions
                                        :can="can"
                                        :edit-url="route('payment-slip.edit', slip.id)"
                                        :delete-url="route('payment-slip.destroy', slip.id)"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import PageHeader from "@/Shared/PageHeader";
import CardHeader from "@/Shared/CardHeader";
import Actions from "@/Shared/Actions";
export default {
    name: "Index",
    props: ['payment_slip', 'can'],
    components: {
        Actions,
        CardHeader,
        PageHeader,
        Authenticated
    },
    data(){
        return {
        }
    },
    methods:{
        imageItems(images){
            let items = []
            if (images !== null && images.length > 0){
                images.forEach(function (value, index){
                    const item = {
                        src: '/' + value.path,
                        thumbnail: '/' + value.path,
                        w: 1200,
                        h: 900,
                        title: 'Will be used for caption'
                    };
                    items.push(item)
                })
            }
            return items;
        },
        statusStyle(status){
            switch (status){
                case 0:
                    return 'border-2 border-yellow-600 color-yellow-600'
                case 1:
                    return 'border-2 border-green-600 color-green-600'
                case 2:
                    return 'border-2 border-red-600 color-red-600'
            }
        },
        statusChange(e){
            const slipId = e.currentTarget.getAttribute('slipid');
            const value = e.currentTarget.value;
            this.$inertia.post(route('payment-slip.change-status',{slip: slipId, status: value}))
        }
    }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<style>
.pswp__img--placeholder--blank{
    display: none !important;
}
.gallery-thumbnail img {
    object-fit: contain !important;
    max-width: 2rem!important;
    margin: 0!important;
    margin-bottom: 0!important;
    margin-top: 0!important;
}
.gallery-thumbnail{
    display: flex!important;
    margin: 0 0.2rem 0.09rem 0!important;
}
.pswp__bg {
    background-color: #fff !important;
}
.Pswp_bg image-zoom-background {
    background-color: #fff !important;
}
.my-gallery{
    display: inline-flex!important;
}
</style>
