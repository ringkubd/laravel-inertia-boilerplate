<template>
    <Head title="Support Tickets Management" />
    <breeze-authenticated-layout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Support Tickets Management
                </h2>
                <button @click="fetchTickets" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100 uppercase tracking-wide">Total Tickets</p>
                                <p class="text-4xl font-bold mt-2">{{ stats.total }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-100 uppercase tracking-wide">In Progress</p>
                                <p class="text-4xl font-bold mt-2">{{ stats.inProgress }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-100 uppercase tracking-wide">Closed</p>
                                <p class="text-4xl font-bold mt-2">{{ stats.closed }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-100 uppercase tracking-wide">Unassigned</p>
                                <p class="text-4xl font-bold mt-2">{{ stats.unassigned }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Panel -->
                <div class="mb-6 bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filters
                        </h3>
                        <span class="text-sm text-gray-500">{{ tickets.length }} results</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="filters.status" @change="fetchTickets" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                <option value="">All Status</option>
                                <option value="open">🔵 Open</option>
                                <option value="in_progress">🟡 In Progress</option>
                                <option value="closed">🟢 Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <select v-model="filters.assigned_to" @change="fetchTickets" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                <option value="">All Assignments</option>
                                <option value="unassigned">❌ Unassigned</option>
                                <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                                    👤 {{ admin.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <input v-model="filters.search" @input="fetchTickets" type="text" placeholder="Search tickets..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select v-model="filters.sort" @change="fetchTickets" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                <option value="-created_at">⬇️ Newest First</option>
                                <option value="created_at">⬆️ Oldest First</option>
                                <option value="-updated_at">🕐 Recently Updated</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button @click="resetFilters" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium shadow-sm hover:shadow">
                                🔄 Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tickets Table -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div v-if="loading" class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>
                    <div v-else>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Ticket Info</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Creator</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Assigned To</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Replies</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-if="tickets.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-xl font-semibold text-gray-500 mb-2">No Tickets Found</p>
                                            <p class="text-gray-400">Try adjusting your search or filter criteria</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-indigo-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-bold">#{{ ticket.id }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ ticket.subject }}</div>
                                                <div class="text-xs text-gray-500">{{ ticket.description.substring(0, 50) }}...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ ticket.user.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ ticket.user.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select
                                            v-model="ticket.status"
                                            @change="updateTicketStatus(ticket.id, $event.target.value)"
                                            class="text-xs font-semibold px-3 py-2 rounded-full border-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all"
                                            :class="statusSelectClass(ticket.status)"
                                        >
                                            <option value="open">🔵 Open</option>
                                            <option value="in_progress">🟡 In Progress</option>
                                            <option value="closed">🟢 Closed</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select
                                            :value="ticket.assigned_to?.id || ''"
                                            @change="assignTicket(ticket.id, $event.target.value)"
                                            class="text-sm px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 hover:border-gray-300"
                                        >
                                            <option value="">❌ Unassigned</option>
                                            <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                                                👤 {{ admin.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                            {{ ticket.messages_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ formatDate(ticket.created_at) }}</span>
                                            <span class="text-xs text-gray-400">{{ formatTime(ticket.created_at) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link :href="`/support-tickets/${ticket.id}`" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm hover:shadow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between bg-white rounded-xl shadow-md p-4">
                    <div class="text-sm text-gray-600 font-medium">
                        Showing <span class="font-bold text-indigo-600">{{ pagination.from }}</span> to <span class="font-bold text-indigo-600">{{ pagination.to }}</span> of <span class="font-bold text-indigo-600">{{ pagination.total }}</span> tickets
                    </div>
                    <div class="flex space-x-2">
                        <Link v-if="pagination.prev_page_url" :href="pagination.prev_page_url" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                           ← Previous
                        </Link>
                        <Link v-if="pagination.next_page_url" :href="pagination.next_page_url" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                            Next →
                        </Link>
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
        },
        admins: {
            type: Array,
            default: () => [],
        }
    },
    data() {
        return {
            tickets: this.initialTickets,
            filters: {
                status: '',
                assigned_to: '',
                search: '',
                sort: '-created_at',
            },
            stats: {
                total: 0,
                inProgress: 0,
                closed: 0,
                unassigned: 0,
            },
            channel: null,
            loading: false,
        };
    },
    mounted() {
        this.calculateStats();
        this.subscribeToUpdates();
    },
    methods: {
        async fetchTickets() {
            try {
                this.loading = true;
                const response = await axios.get(route('support-tickets.admin'), {
                    params: this.filters
                });
                this.tickets = response.data.tickets;
                this.pagination = response.data.pagination;
                this.calculateStats();
            } catch (error) {
                console.error('Error fetching tickets:', error);
            } finally {
                this.loading = false;
            }
        },
        async updateTicketStatus(ticketId, status) {
            try {
                const response = await axios.patch(route('support-tickets.update', ticketId), { status });
                const ticketIndex = this.tickets.findIndex(t => t.id === ticketId);
                if (ticketIndex !== -1 && response.data.ticket) {
                    this.tickets.splice(ticketIndex, 1, response.data.ticket);
                }
                this.calculateStats();
            } catch (error) {
                console.error('Error updating ticket:', error);
            }
        },
        async assignTicket(ticketId, adminId) {
            try {
                const assignedId = adminId === '' ? null : parseInt(adminId);
                const response = await axios.patch(route('support-tickets.update', ticketId), { assigned_to: assignedId });
                const ticketIndex = this.tickets.findIndex(t => t.id === ticketId);
                if (ticketIndex !== -1 && response.data.ticket) {
                    this.tickets.splice(ticketIndex, 1, response.data.ticket);
                }
                this.calculateStats();
            } catch (error) {
                console.error('Error assigning ticket:', error);
            }
        },
        resetFilters() {
            this.filters = {
                status: '',
                assigned_to: '',
                search: '',
                sort: '-created_at',
            };
            this.fetchTickets();
        },
        calculateStats() {
            this.stats = {
                total: this.tickets.length,
                inProgress: this.tickets.filter(t => t.status === 'in_progress').length,
                closed: this.tickets.filter(t => t.status === 'closed').length,
                unassigned: this.tickets.filter(t => !t.assigned_to).length,
            };
        },
        subscribeToUpdates() {
            if (window.Echo) {
                this.channel = window.Echo.channel('support-tickets');

                this.channel.listen('.TicketCreated', (data) => {
                    console.log('TicketCreated event received:', data);
                    this.tickets.unshift(data.ticket);
                    this.calculateStats();
                });

                this.channel.listen('.TicketStatusUpdated', (data) => {
                    console.log('TicketStatusUpdated event received:', data);
                    const index = this.tickets.findIndex(t => t.id === data.ticket.id);
                    if (index !== -1) {
                        this.tickets.splice(index, 1, data.ticket);
                        this.calculateStats();
                    }
                });

                this.channel.listen('.TicketAssigned', (data) => {
                    console.log('TicketAssigned event received:', data);
                    const index = this.tickets.findIndex(t => t.id === data.ticket.id);
                    if (index !== -1) {
                        this.tickets.splice(index, 1, data.ticket);
                        this.calculateStats();
                    }
                });
            } else {
                console.error('Echo is not initialized');
            }
        },
        statusSelectClass(status) {
            const baseClass = "px-3 py-2 rounded-lg border-2 focus:ring-2 focus:ring-offset-1 transition-all duration-200";
            const statusClasses = {
                'open': 'bg-blue-50 border-blue-200 text-blue-700 hover:border-blue-300 focus:ring-blue-400',
                'in_progress': 'bg-yellow-50 border-yellow-200 text-yellow-700 hover:border-yellow-300 focus:ring-yellow-400',
                'closed': 'bg-green-50 border-green-200 text-green-700 hover:border-green-300 focus:ring-green-400'
            };
            return `${baseClass} ${statusClasses[status] || 'bg-gray-50 border-gray-200 text-gray-700'}`;
        },
        formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        },
        statusClass(status) {
            const classes = {
                'open': 'bg-blue-100 text-blue-800 border-blue-300',
                'in_progress': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'closed': 'bg-green-100 text-green-800 border-green-300',
            };
            return classes[status] || 'bg-gray-100 text-gray-800 border-gray-300';
        },
        formatDate(date) {
            return moment(date).format('MMM DD, YYYY');
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
