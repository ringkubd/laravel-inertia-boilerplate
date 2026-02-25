<template>
    <Head :title="`Ticket #${ticket.id}`" />
    <breeze-authenticated-layout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">Support Ticket #{{ ticket.id }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ ticket.subject }}</p>
                </div>
                <Link v-if="canManage" href="/support-tickets/admin/dashboard" class="inline-flex items-center px-5 py-2.5 text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow text-sm font-medium">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </Link>
                <Link v-else href="/support-tickets" class="inline-flex items-center px-5 py-2.5 text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow text-sm font-medium">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Tickets
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Ticket Header -->
                    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg p-8 border border-blue-100">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ ticket.subject }}</h2>
                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    <div class="flex items-center px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3">
                                            {{ ticket.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Created by</p>
                                            <p class="font-semibold text-gray-900">{{ ticket.user.name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-700">{{ formatDate(ticket.created_at) }}</span>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold shadow-sm border-2" :class="statusBadgeClass(ticket.status)">
                                            {{ formatStatus(ticket.status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Description</h3>
                            <p class="text-gray-700 text-base whitespace-pre-wrap leading-relaxed">{{ ticket.description }}</p>
                        </div>

                        <!-- Attachments -->
                        <div v-if="ticket.attachments.length > 0" class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">📎 Attachments</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div v-for="attachment in ticket.attachments" :key="attachment.id" class="group border-2 border-gray-200 rounded-xl p-4 hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200">
                                    <a :href="attachment.filepath" target="_blank" class="flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                        <svg class="h-5 w-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm truncate">{{ attachment.filename }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">💬 Conversation</h3>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">
                                {{ messages.length }} {{ messages.length === 1 ? 'Message' : 'Messages' }}
                            </span>
                        </div>

                        <!-- Messages List -->
                        <div ref="messageContainer" class="space-y-4 mb-6 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <div v-if="messages.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-500 font-medium">No messages yet</p>
                                <p class="text-gray-400 text-sm mt-1">Start the conversation by replying below</p>
                            </div>

                            <div v-for="message in messages" :key="message.id" class="flex" :class="{ 'justify-end': message.user_id === $page.props.user.id }">
                                <div class="max-w-lg">
                                    <div
                                        class="px-5 py-3 rounded-2xl shadow-md"
                                        :class="message.user_id === $page.props.user.id
                                            ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-br-md'
                                            : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-900 rounded-bl-md border border-gray-300'
                                        "
                                    >
                                        <div class="flex items-center mb-2">
                                            <div
                                                class="h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold mr-2"
                                                :class="message.user_id === $page.props.user.id ? 'bg-white text-indigo-600' : 'bg-indigo-600 text-white'"
                                            >
                                                {{ message.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold" :class="message.user_id === $page.props.user.id ? 'text-white' : 'text-gray-900'">
                                                    {{ message.user.name }}
                                                </p>
                                                <p class="text-xs" :class="message.user_id === $page.props.user.id ? 'text-indigo-200' : 'text-gray-500'">
                                                    {{ message.sender_type }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-sm leading-relaxed">{{ message.message }}</p>

                                        <!-- Message Attachments -->
                                        <div v-if="message.attachments.length > 0" class="mt-3 pt-3 border-t" :class="message.user_id === $page.props.user.id ? 'border-indigo-500' : 'border-gray-300'">
                                            <div v-for="att in message.attachments" :key="att.id" class="mb-1">
                                                <a :href="att.filepath" target="_blank"
                                                   class="text-xs font-medium hover:underline flex items-center"
                                                   :class="message.user_id === $page.props.user.id ? 'text-indigo-100' : 'text-indigo-600'">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    {{ att.filename }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 px-2" :class="{ 'text-right': message.user_id === $page.props.user.id }">
                                        {{ formatDate(message.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Reply Form -->
                        <form @submit.prevent="sendReply" class="border-t-2 border-gray-200 pt-6 mt-6">
                            <div class="mb-4">
                                <textarea
                                    v-model="reply.message"
                                    rows="4"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    placeholder="Type your reply here..."
                                    required
                                ></textarea>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-4">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-indigo-400 transition-all duration-200 bg-gradient-to-br from-gray-50 to-blue-50"
                                    @dragover.prevent="dragover = true"
                                    @dragleave="dragover = false"
                                    @drop.prevent="handleDrop"
                                    :class="{ 'border-indigo-500 bg-indigo-50': dragover }">

                                    <input
                                        ref="fileInput"
                                        type="file"
                                        @change="handleFileSelect"
                                        class="hidden"
                                    >

                                    <label class="cursor-pointer text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-8-12v12m0 0l-3-3m3 3l3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="text-sm text-gray-700 font-medium">
                                            <span class="text-indigo-600 hover:text-indigo-700 cursor-pointer font-bold" @click="$refs.fileInput.click()">Click to upload</span>
                                            or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG (max 10MB)</p>
                                    </label>
                                </div>

                                <div v-if="reply.attachment" class="mt-3 flex items-center justify-between bg-indigo-50 border-2 border-indigo-200 p-3 rounded-xl">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 font-medium">{{ reply.attachment.name }}</span>
                                    </div>
                                    <button type="button" @click="reply.attachment = null" class="text-red-500 hover:text-red-700 hover:bg-red-100 p-1 rounded-lg transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="replyLoading"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl font-medium"
                                >
                                    <svg v-if="!replyLoading" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <svg v-else class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ replyLoading ? 'Sending...' : 'Send Reply' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-lg p-6 border-2 border-indigo-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide flex items-center">
                            <svg class="h-5 w-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </h3>
                        <div v-if="canManage" class="space-y-2">
                            <select
                                :value="ticket.status"
                                @change="updateStatus($event.target.value)"
                                :class="statusSelectClass(ticket.status)"
                                class="w-full px-4 py-3 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 transition-all duration-200 font-medium"
                            >
                                <option value="open">🔵 Open</option>
                                <option value="in_progress">🟡 In Progress</option>
                                <option value="closed">🟢 Closed</option>
                            </select>
                        </div>
                        <div v-else>
                            <span class="inline-flex items-center w-full justify-center px-4 py-3 rounded-xl text-sm font-bold border-2 shadow-sm" :class="statusBadgeClass(ticket.status)">
                                {{ formatStatus(ticket.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Assignment Card -->
                    <div v-if="canManage" class="bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-lg p-6 border-2 border-yellow-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide flex items-center">
                            <svg class="h-5 w-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Assign To
                        </h3>
                        <select
                            :value="ticket.assigned_to?.id || ''"
                            @change="assignTicket($event.target.value)"
                            class="w-full px-4 py-3 border-2 border-yellow-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-all duration-200 bg-white hover:border-yellow-300 font-medium"
                        >
                            <option value="">❌ Unassigned</option>
                            <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                                👤 {{ admin.name }}
                            </option>
                        </select>
                        <div v-if="ticket.assignedTo" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs text-gray-600">Currently assigned to</p>
                            <p class="text-sm font-bold text-gray-900 mt-1">👤 {{ ticket.assignedTo.name }}</p>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg p-6 border-2 border-green-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide flex items-center">
                            <svg class="h-5 w-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Details
                        </h3>
                        <dl class="space-y-4 text-sm">
                            <div class="pb-3 border-b border-green-100">
                                <dt class="text-gray-600 text-xs uppercase tracking-wide">Created</dt>
                                <dd class="font-bold text-gray-900 mt-1.5 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ formatFullDate(ticket.created_at) }}
                                </dd>
                            </div>
                            <div class="pb-3 border-b border-green-100">
                                <dt class="text-gray-600 text-xs uppercase tracking-wide">Last Updated</dt>
                                <dd class="font-bold text-gray-900 mt-1.5 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    {{ formatFullDate(ticket.updated_at) }}
                                </dd>
                            </div>
                            <div class="pb-3 border-b border-green-100">
                                <dt class="text-gray-600 text-xs uppercase tracking-wide">Total Messages</dt>
                                <dd class="font-bold text-gray-900 mt-1.5 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    {{ messages.length }}
                                </dd>
                            </div>
                            <div v-if="ticket.assignedTo">
                                <dt class="text-gray-600 text-xs uppercase tracking-wide">Assigned To</dt>
                                <dd class="font-bold text-gray-900 mt-1.5 flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold mr-2">
                                        {{ ticket.assignedTo.name.charAt(0).toUpperCase() }}
                                    </div>
                                    {{ ticket.assignedTo.name }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </breeze-authenticated-layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Link,
        Head,
    },
    props: {
        ticket: {
            type: Object,
            required: true,
        },
        messages: {
            type: Array,
            default: () => [],
        },
        admins: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            reply: {
                message: '',
                attachment: null,
            },
            replyLoading: false,
            dragover: false,
            canManage: false,
            channel: null,
        };
    },
    mounted() {
        this.checkPermissions();
        this.subscribeToUpdates();
        this.scrollToBottom();
    },
    methods: {
        checkPermissions() {
            const allowedRoles = ['Admin', 'Super Admin', 'Account'];
            this.canManage = this.$page.props.user.roles.some(role => allowedRoles.includes(role.name));
        },
        async sendReply() {
            if (this.reply.message.trim() === '') return;

            this.replyLoading = true;

            try {
                const formData = new FormData();
                formData.append('message', this.reply.message);
                formData.append('sender_type', this.canManage ? 'admin' : 'user');

                if (this.reply.attachment) {
                    formData.append('attachment', this.reply.attachment);
                }

                const response = await axios.post(
                    route('ticket-messages.store', this.ticket.id),
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                );

                this.reply.message = '';
                this.reply.attachment = null;
                this.scrollToBottom();
            } catch (error) {
                console.error('Error sending reply:', error);
            } finally {
                this.replyLoading = false;
            }
        },
        async updateStatus(status) {
            try {
                const response = await axios.patch(
                    route('support-tickets.update', this.ticket.id),
                    { status: status }
                );
                if (response.data.ticket) {
                    this.ticket.status = response.data.ticket.status;
                }
            } catch (error) {
                console.error('Error updating status:', error);
            }
        },
        async assignTicket(adminId) {
            try {
                const assignedId = adminId === '' ? null : parseInt(adminId);
                const response = await axios.patch(
                    route('support-tickets.update', this.ticket.id),
                    { assigned_to: assignedId }
                );
                if (response.data.ticket) {
                    this.ticket.assigned_to = response.data.ticket.assigned_to;
                    this.ticket.assignedTo = response.data.ticket.assignedTo;
                }
            } catch (error) {
                console.error('Error assigning ticket:', error);
            }
        },
        subscribeToUpdates() {
            if (window.Echo) {
                this.channel = window.Echo.private(`ticket.${this.ticket.id}`);

                this.channel.listen('.TicketMessageCreated', (data) => {
                    console.log('TicketMessageCreated event received:', data);
                    // Add new message to the list if it's not already there
                    if (data.message && !this.messages.find(m => m.id === data.message.id)) {
                        this.messages.push(data.message);
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                });

                this.channel.listen('.TicketStatusUpdated', (data) => {
                    console.log('TicketStatusUpdated event received:', data);
                    if (data.ticket) {
                        this.ticket.status = data.ticket.status;
                    }
                });

                this.channel.listen('.TicketAssigned', (data) => {
                    console.log('TicketAssigned event received:', data);
                    if (data.ticket) {
                        this.ticket.assigned_to = data.ticket.assigned_to;
                        this.ticket.assignedTo = data.ticket.assignedTo;
                    }
                });
            } else {
                console.error('Echo is not initialized');
            }
        },
        handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                this.reply.attachment = files[0];
            }
        },
        handleDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.reply.attachment = files[0];
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messageContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        },
        formatDate(date) {
            return moment(date).fromNow();
        },
        formatFullDate(date) {
            return moment(date).format('MMM DD, YYYY h:mm A');
        },
        formatStatus(status) {
            return status.replace('_', ' ').toUpperCase();
        },
        statusClass(status) {
            const classes = {
                'open': 'bg-blue-100 text-blue-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'closed': 'bg-green-100 text-green-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        statusSelectClass(status) {
            const classes = {
                'open': 'bg-blue-50 border-2 border-blue-300 text-blue-800 focus:ring-blue-400',
                'in_progress': 'bg-yellow-50 border-2 border-yellow-300 text-yellow-800 focus:ring-yellow-400',
                'closed': 'bg-green-50 border-2 border-green-300 text-green-800 focus:ring-green-400',
            };
            return classes[status] || 'bg-gray-50 border-2 border-gray-300 text-gray-800';
        },
        statusBadgeClass(status) {
            const classes = {
                'open': 'bg-blue-100 text-blue-800 border-blue-300',
                'in_progress': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'closed': 'bg-green-100 text-green-800 border-green-300',
            };
            return classes[status] || 'bg-gray-100 text-gray-800 border-gray-300';
        }
    },
    beforeUnmount() {
        if (this.channel) {
            window.Echo.leave(`ticket.${this.ticket.id}`);
        }
    }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
