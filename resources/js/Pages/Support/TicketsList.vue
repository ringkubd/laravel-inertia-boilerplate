<template>
    <Head title="Support Tickets" />
    <breeze-authenticated-layout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Support Tickets</h2>
                <Link href="/support-tickets/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    Create Ticket
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <!-- Filters -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <select v-model="filters.status" @change="fetchTickets" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <input v-model="filters.search" @keyup.debounce="fetchTickets" type="text" placeholder="Search tickets..." class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Tickets List -->
                <div class="grid grid-cols-1 gap-4">
                    <div v-if="tickets.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tickets</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new support ticket.</p>
                    </div>

                    <div v-for="ticket in tickets" :key="ticket.id" class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 border-l-4" :class="statusBorderClass(ticket.status)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ ticket.subject }}</h3>
                                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="statusClass(ticket.status)">
                                        {{ formatStatus(ticket.status) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ truncate(ticket.description, 100) }}</p>
                                <div class="mt-4 flex items-center space-x-6 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ ticket.user.name }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ formatDate(ticket.created_at) }}
                                    </div>
                                    <div class="flex items-center" v-if="ticket.assigned_to">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        {{ ticket.assignedTo?.name }}
                                    </div>
                                </div>
                            </div>
                            <Link :href="`/support-tickets/${ticket.id}`" class="ml-4 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} tickets
                    </div>
                    <div class="flex space-x-2">
                        <Link v-if="pagination.prev_page_url" :href="pagination.prev_page_url" class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">Previous</Link>
                        <Link v-if="pagination.next_page_url" :href="pagination.next_page_url" class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">Next</Link>
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
        initialTickets: {
            type: Array,
            default: () => [],
        },
        pagination: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            tickets: this.initialTickets,
            filters: {
                status: '',
                search: '',
            },
            channel: null,
        };
    },
    mounted() {
        this.subscribeToUpdates();
    },
    methods: {
        async fetchTickets() {
            try {
                const response = await axios.get(route('support-tickets.index'), {
                    params: this.filters
                });
                this.tickets = response.data.tickets;
                this.pagination = response.data.pagination;
            } catch (error) {
                console.error('Error fetching tickets:', error);
            }
        },
        subscribeToUpdates() {
            if (window.Echo) {
                this.channel = window.Echo.channel('support-tickets');

                this.channel.listen('.TicketCreated', (data) => {
                    console.log('TicketCreated event received:', data);
                    if (data.ticket && !this.tickets.find(t => t.id === data.ticket.id)) {
                        this.tickets.unshift(data.ticket);
                    }
                });

                this.channel.listen('.TicketStatusUpdated', (data) => {
                    console.log('TicketStatusUpdated event received:', data);
                    const index = this.tickets.findIndex(t => t.id === data.ticket.id);
                    if (index !== -1) {
                        this.tickets.splice(index, 1, data.ticket);
                    }
                });

                this.channel.listen('.TicketAssigned', (data) => {
                    console.log('TicketAssigned event received:', data);
                    const index = this.tickets.findIndex(t => t.id === data.ticket.id);
                    if (index !== -1) {
                        this.tickets.splice(index, 1, data.ticket);
                    }
                });
            } else {
                console.error('Echo is not initialized');
            }
        },
        statusClass(status) {
            const classes = {
                'open': 'bg-blue-100 text-blue-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'closed': 'bg-green-100 text-green-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        statusBorderClass(status) {
            const classes = {
                'open': 'border-blue-500',
                'in_progress': 'border-yellow-500',
                'closed': 'border-green-500',
            };
            return classes[status] || 'border-gray-500';
        },
        formatStatus(status) {
            return status.replace('_', ' ').toUpperCase();
        },
        truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        formatDate(date) {
            return moment(date).fromNow();
        }
    },
    beforeUnmount() {
        if (this.channel) {
            window.Echo.leave('support-tickets');
        }
    }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
