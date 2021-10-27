<template>
    <div>
        <nav class="bg-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex justify-between">
                    <div class="flex space-x-7">
                        <!--Website Logo-->
                        <inertia-link :href="route('dashboard')" class="text-sm text-gray-700 underline mb-2">
                            <ApplicationLogo/>
                        </inertia-link>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                            <inertia-link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-gray-700 underline">
                                Dashboard
                            </inertia-link>

                            <template v-else>
                                <inertia-link :href="route('login')" class="text-sm text-gray-700 underline">
                                    Log in
                                </inertia-link>

                                <inertia-link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
                                    Register
                                </inertia-link>
                            </template>
                        </div>
                    </div>
                    <div class="md:hidden flex items-center">
                        <button class="outline-none menu-button">
                            <svg class="w-6 h-6 text-gray-500" x-show="! showMenu" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round" stroke-width="2" viewBox="0 00 24 24" stroke="currentColor">
                                <path d="m4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="hidden mobile-menu">
                        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                            <inertia-link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-gray-700 underline">
                                Dashboard
                            </inertia-link>

                            <template v-else>
                                <inertia-link :href="route('login')" class="text-sm text-gray-700 underline">
                                    Log in
                                </inertia-link>

                                <inertia-link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
                                    Register
                                </inertia-link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        .
    </div>
</template>

<style scoped>
.bg-gray-100 {
    background-color: #f7fafc;
    background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
}

.border-gray-200 {
    border-color: #edf2f7;
    border-color: rgba(237, 242, 247, var(--tw-border-opacity));
}

.text-gray-400 {
    color: #cbd5e0;
    color: rgba(203, 213, 224, var(--tw-text-opacity));
}

.text-gray-500 {
    color: #a0aec0;
    color: rgba(160, 174, 192, var(--tw-text-opacity));
}

.text-gray-600 {
    color: #718096;
    color: rgba(113, 128, 150, var(--tw-text-opacity));
}

.text-gray-700 {
    color: #4a5568;
    color: rgba(74, 85, 104, var(--tw-text-opacity));
}

.text-gray-900 {
    color: #1a202c;
    color: rgba(26, 32, 44, var(--tw-text-opacity));
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-gray-800 {
        background-color: #2d3748;
        background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
    }

    .dark\:bg-gray-900 {
        background-color: #1a202c;
        background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
    }

    .dark\:border-gray-700 {
        border-color: #4a5568;
        border-color: rgba(74, 85, 104, var(--tw-border-opacity));
    }

    .dark\:text-white {
        color: #fff;
        color: rgba(255, 255, 255, var(--tw-text-opacity));
    }

    .dark\:text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }
}
</style>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo";
export default {
    components: {ApplicationLogo},
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    },
    mounted(){
        const btn = document.querySelector('button.menu-button');
        const menu = document.querySelector(".mobile-menu");
        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        })
    }
}
</script>
