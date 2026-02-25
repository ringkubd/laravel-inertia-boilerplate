<template>
    <Head title="Create Support Ticket" />
    <breeze-authenticated-layout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Support Ticket</h2>
                <Link href="/support-tickets" class="px-4 py-2 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-sm">
                    Back to Tickets
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="bg-white rounded-lg shadow px-6 py-8">
                    <form @submit.prevent="submitForm">
                        <!-- Subject -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Brief description of your issue"
                                required
                            >
                            <div v-if="errors.subject" class="mt-1 text-red-500 text-sm">{{ errors.subject }}</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Provide detailed information about your issue..."
                                required
                            ></textarea>
                            <div v-if="errors.description" class="mt-1 text-red-500 text-sm">{{ errors.description }}</div>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-6">
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                Attachments (Optional)
                            </label>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors bg-gray-50"
                                @dragover="dragover = true"
                                @dragleave="dragover = false"
                                @drop.prevent="handleDrop"
                                :class="{ 'border-blue-500 bg-blue-50': dragover }">

                                <input
                                    id="attachments"
                                    ref="fileInput"
                                    type="file"
                                    multiple
                                    @change="handleFileSelect"
                                    class="hidden"
                                >

                                <label for="attachments" class="cursor-pointer text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-8-12v12m0 0l-3-3m3 3l3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-600">PNG, JPG, PDF up to 10MB</p>
                                </label>
                            </div>

                            <!-- File List -->
                            <div v-if="form.attachments.length > 0" class="mt-4 space-y-2">
                                <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ file.name }}</span>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div v-if="errors.attachments" class="mt-1 text-red-500 text-sm">{{ errors.attachments }}</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <Link href="/support-tickets" class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="loading" class="inline-block mr-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                {{ loading ? 'Creating...' : 'Create Ticket' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </breeze-authenticated-layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Link,
        Head,
    },
    data() {
        return {
            form: {
                subject: '',
                description: '',
                attachments: [],
            },
            errors: {},
            loading: false,
            dragover: false,
        };
    },
    methods: {
        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const formData = new FormData();
                formData.append('subject', this.form.subject);
                formData.append('description', this.form.description);

                this.form.attachments.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file);
                });

                const response = await axios.post(route('support-tickets.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                });

                // Redirect to ticket detail
                window.location.href = route('support-tickets.show', response.data.ticket.id);
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error('Error creating ticket:', error);
                }
            } finally {
                this.loading = false;
            }
        },
        handleFileSelect(event) {
            const files = event.target.files;
            this.addFiles(files);
        },
        handleDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            this.addFiles(files);
        },
        addFiles(files) {
            for (let i = 0; i < files.length; i++) {
                if (this.form.attachments.length < 5) {
                    this.form.attachments.push(files[i]);
                }
            }
        },
        removeFile(index) {
            this.form.attachments.splice(index, 1);
        }
    }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
