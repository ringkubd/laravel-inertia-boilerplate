<template>
    <Head>
        <title>Create Notesheet Template</title>
    </Head>
    <Authenticated>
        <template #header>
            <PageHeader>Create Notesheet Template</PageHeader>
        </template>
        <template #default>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <CardHeader>
                            <template #first>
                                <Back :back-url="route('note_sheet_template.index')"/>
                            </template>
                        </CardHeader>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input v-model="form.title" type="text" class="form-control" required>
                                <div class="text-danger" v-if="errors.title">{{ errors.title }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fee Types (optional)</label>
                                <select v-model="form.fee_type" multiple class="form-control" style="min-height: 120px;">
                                    <option v-for="type in fee_types" :key="type.value" :value="type.value">{{ type.label }}</option>
                                </select>
                                <div class="text-danger" v-if="errors.fee_type">{{ errors.fee_type }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea v-model="form.content" rows="12" class="form-control" required></textarea>
                                <div class="text-danger" v-if="errors.content">{{ errors.content }}</div>
                            </div>
                            <button type="submit" class="btn btn-success">Save Template</button>
                        </form>
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
import Back from "@/Shared/Back";

export default {
    name: 'NoteSheetTemplateCreate',
    props: {
        fee_types: Array,
        note_template: Object,
        selected_fee_types: Array,
        errors: Object,
        can: Object,
    },
    components: {Back, CardHeader, PageHeader, Authenticated},
    data() {
        return {
            form: {
                title: this.note_template?.title || '',
                content: this.note_template?.content || '',
                fee_type: this.selected_fee_types || [],
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('note_sheet_template.store'), this.form);
        }
    }
}
</script>
