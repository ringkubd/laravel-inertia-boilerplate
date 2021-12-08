<template>
    <Head title="Dashboard"></Head>
    <div>
        <chat  v-if="!$page.props.menu_permission.support"/>
        <div class="min-h-screen bg-[#36AFAD]">
            <!--     from-[#7AE5F5] via-[#97EBF4] to-[#C9F6FF]       -->
            <nav class="bg-gradient-to-br from-green-500 via-yellow-50 to-green-500  animate-gradient-x shadow-lg text-black border-b-4 opacity-100 border-gray-600 sticky top-0 z-50">
                <!-- Primary Navigation Menu -->
                <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex flex-auto items-center">
                                <inertia-link :href="route('dashboard')">
                                    <img src="/isdb-bisew.png" width="55" class="object-fill thumbnail animate-tilt" alt="">
                                </inertia-link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="
                                    hidden
                                    space-x-2
                                    sm:-my-px sm:ml-10 sm:flex
                                    rounded-md
                                "
                            >
                                <breeze-nav-link
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="rounded-md"
                                >
                                    Dashboard
                                </breeze-nav-link>

                                <div class="hidden sm:flex sm:items-center sm:ml-6" v-if="$page.props.menu_permission.user_management">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                User's Management

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('users.index')"
                                                :active="route().current('users.*')"
                                            >
                                                User
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('user_role.index')"
                                                :active="route().current('user_role.*')"
                                            >
                                                User's Role
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('roles.index')"
                                                :active="route().current('roles.*')"
                                            >
                                                Role
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('permission.index')"
                                                :active="route().current('permission.*')"
                                            >
                                                Permission
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>

                                <div class="hidden sm:flex sm:items-center sm:ml-6"  v-if="$page.props.menu_permission.blog_management">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Blog

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('post.index')"
                                                :active="route().current('post.*')"
                                            >
                                                Post
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('page.index')"
                                                :active="route().current('page.*')"
                                            >
                                                Pages
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('category.index')"
                                                :active="route().current('category.*')"
                                            >
                                                Category
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>

                                <div class="hidden sm:flex sm:items-center sm:ml-6"  v-if="$page.props.menu_permission.madrasah || $page.props.menu_permission.teacher || $page.props.menu_permission.madrasah_student || $page.props.menu_permission.madrasah_result">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Madrasa

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('madrasa.index')"
                                                :active="route().current('madrasa.*')"
                                                v-if="$page.props.menu_permission.madrasah"
                                            >
                                                Madrasa
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('teacher.index')"
                                                :active="route().current('teacher.*')"
                                                v-if="$page.props.menu_permission.teacher"
                                            >
                                                Teacher
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('madrasa.student.index')"
                                                :active="route().current('madrasa.student.*')"
                                                v-if="$page.props.menu_permission.madrasah_student"
                                            >
                                                Student's
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('madrasa.result.index')"
                                                :active="route().current('madrasa.result.*')"
                                                v-if="$page.props.menu_permission.madrasah_result"
                                            >
                                                Result's
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>
                                <div class="hidden sm:flex sm:items-center sm:ml-6" v-if="$page.props.menu_permission.academic_session || $page.props.menu_permission.trade">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Accessibility

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('academic_session.index')"
                                                :active="route().current('academic_session.*')"
                                                v-if="$page.props.menu_permission.academic_session"
                                            >
                                                Sessions
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('trade.index')"
                                                :active="route().current('trade.*')"
                                                v-if="$page.props.menu_permission.trade"
                                            >
                                                Trade
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>

                                <div class="hidden sm:flex sm:items-center sm:ml-6"  v-if="$page.props.menu_permission.polytechnic || $page.props.menu_permission.polytechnic_student || $page.props.menu_permission.polytechnic_result">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Polytechnic

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('polytechnic.index')"
                                                :active="route().current('polytechnic.*')"
                                                v-if="$page.props.menu_permission.polytechnic"
                                            >
                                                Polytechnic
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('polytechnic.result.index')"
                                                :active="route().current('polytechnic.result.*')"
                                                v-if="$page.props.menu_permission.polytechnic_result"
                                            >
                                                Results
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('polytechnic.student.index')"
                                                :active="route().current('polytechnic.madrasa.student.*')"
                                                v-if="$page.props.menu_permission.polytechnic_student"
                                            >
                                                Student's
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>
                                <div class="hidden sm:flex sm:items-center sm:ml-6"  v-if="$page.props.menu_permission.invoice || $page.props.menu_permission.fee">
                                    <breeze-dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Bill

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <breeze-dropdown-link
                                                :href="route('fee.index')"
                                                :active="route().current('fee.*')"
                                                v-if="$page.props.menu_permission.fee"
                                            >
                                                Fee's
                                            </breeze-dropdown-link>
                                            <breeze-dropdown-link
                                                :href="route('invoice.index')"
                                                :active="route().current('invoice.*')"
                                                v-if="$page.props.menu_permission.invoice"
                                            >
                                                Invoice's
                                            </breeze-dropdown-link>
                                        </template>
                                    </breeze-dropdown>
                                </div>
                                <breeze-nav-link
                                    :href="route('admission.index')"
                                    :active="route().current('admission.index')"
                                    class="rounded-md text-black-700"
                                    v-if="$page.props.menu_permission.admission"
                                >
                                    Admission
                                </breeze-nav-link>
                                <breeze-nav-link
                                    :href="route('support.index')"
                                    :active="route().current('support.index')"
                                    class="rounded-md text-black-700"
                                    v-if="$page.props.menu_permission.support"
                                >
                                    Support
                                </breeze-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div align="left" width="10" class="border-1 border-green-400 border-solid rounded">
                                <span class="inline-flex border-1px rounded-md"> {{ this.$store.state.onlineFriends.length }} </span>
                            </div>
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <breeze-dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <breeze-dropdown-link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            @click="logout"
                                        >
                                            Log Out
                                        </breeze-dropdown-link>
                                    </template>
                                </breeze-dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    p-2
                                    rounded-md
                                    text-gray-400
                                    hover:text-gray-500 hover:bg-gray-100
                                    focus:outline-none
                                    focus:bg-gray-100
                                    focus:text-gray-500
                                    transition
                                    duration-150
                                    ease-in-out
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <breeze-responsive-nav-link
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </breeze-responsive-nav-link>

                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0" v-if="$page.props.menu_permission.user_management">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-sm">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-sm
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                User's Management

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('users.index')"
                                        :active="route().current('users.*')"
                                    >
                                        User
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('user_role.index')"
                                        :active="route().current('user_role.*')"
                                    >
                                        User's Role
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('roles.index')"
                                        :active="route().current('roles.*')"
                                    >
                                        Role
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('permission.index')"
                                        :active="route().current('permission.*')"
                                    >
                                        Permission
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>

                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0"  v-if="$page.props.menu_permission.blog_management">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Blog

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('post.index')"
                                        :active="route().current('post.*')"
                                    >
                                        Post
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('page.index')"
                                        :active="route().current('page.*')"
                                    >
                                        Pages
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('category.index')"
                                        :active="route().current('category.*')"
                                    >
                                        Category
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>

                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0"  v-if="$page.props.menu_permission.madrasah || $page.props.menu_permission.teacher || $page.props.menu_permission.madrasah_student || $page.props.menu_permission.madrasah_result">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Madrasa

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('madrasa.index')"
                                        :active="route().current('madrasa.*')"
                                        v-if="$page.props.menu_permission.madrasah"
                                    >
                                        Madrasa
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('teacher.index')"
                                        :active="route().current('teacher.*')"
                                        v-if="$page.props.menu_permission.teacher"
                                    >
                                        Teacher
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('madrasa.student.index')"
                                        :active="route().current('madrasa.student.*')"
                                        v-if="$page.props.menu_permission.madrasah_student"
                                    >
                                        Student's
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('madrasa.result.index')"
                                        :active="route().current('madrasa.result.*')"
                                        v-if="$page.props.menu_permission.madrasah_result"
                                    >
                                        Result's
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>
                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0" v-if="$page.props.menu_permission.academic_session || $page.props.menu_permission.trade">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Accessibility

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('academic_session.index')"
                                        :active="route().current('academic_session.*')"
                                        v-if="$page.props.menu_permission.academic_session"
                                    >
                                        Sessions
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('trade.index')"
                                        :active="route().current('trade.*')"
                                        v-if="$page.props.menu_permission.trade"
                                    >
                                        Trade
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>

                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0"  v-if="$page.props.menu_permission.polytechnic || $page.props.menu_permission.polytechnic_student || $page.props.menu_permission.polytechnic_result">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Polytechnic

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('polytechnic.index')"
                                        :active="route().current('polytechnic.*')"
                                        v-if="$page.props.menu_permission.polytechnic"
                                    >
                                        Polytechnic
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('polytechnic.result.index')"
                                        :active="route().current('polytechnic.result.*')"
                                        v-if="$page.props.menu_permission.polytechnic_result"
                                    >
                                        Results
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('polytechnic.student.index')"
                                        :active="route().current('polytechnic.madrasa.student.*')"
                                        v-if="$page.props.menu_permission.polytechnic_student"
                                    >
                                        Student's
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>
                        <div class="sm:flex sm:items-center sm:ml-6 sm:border-0"  v-if="$page.props.menu_permission.invoice || $page.props.menu_permission.fee">
                            <breeze-dropdown align="right" width="48">
                                <template #trigger>
                                        <span class="inline-flex items-center rounded-md">
                                            <button
                                                type="button"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-2
                                                    border border-transparent
                                                    text-sm
                                                    leading-4
                                                    font-medium
                                                    rounded-md
                                                    text-gray-500
                                                    bg-white
                                                    hover:text-gray-700
                                                    focus:outline-none
                                                    focus:ring-2
                                                    focus:ring-offset-2
                                                    focus:ring-offset-gray-100
                                                    focus:ring-indigo-500
                                                    transition
                                                    ease-in-out
                                                    duration-150
                                                "
                                            >
                                                Bill

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                </template>

                                <template #content>
                                    <breeze-dropdown-link
                                        :href="route('fee.index')"
                                        :active="route().current('fee.*')"
                                        v-if="$page.props.menu_permission.fee"
                                    >
                                        Fee's
                                    </breeze-dropdown-link>
                                    <breeze-dropdown-link
                                        :href="route('invoice.index')"
                                        :active="route().current('invoice.*')"
                                        v-if="$page.props.menu_permission.invoice"
                                    >
                                        Invoice's
                                    </breeze-dropdown-link>
                                </template>
                            </breeze-dropdown>
                        </div>

                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <breeze-responsive-nav-link
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </breeze-responsive-nav-link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="container-fluid animate-gradient-x bg-gradient-to-r from-[#36AFAD] via-yellow-100 to-[#36C57F]" v-if="$slots.header">
                <div class="max-w-8xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    <div v-if="$page.props.flash.message" class="alert">
                        {{ $page.props.flash.message }}
                    </div>
                    <div
                        v-if="$page.props.flash.error"
                        class="alert alert-error"
                    >
                        {{ $page.props.flash.error }}
                    </div>
                    <div
                        v-if="$page.props.flash.success"
                        class="alert alert-success"
                    >
                        {{ $page.props.flash.success }}
                    </div>
                    <slot name="header" />
                </div>
            </header>
            <!-- Page Content -->
            <main>
                <slot :onlineFriends="onlineFriends" :offlineFriends="offlineFriends" />
            </main>
        </div>
        <footer class="bg-gradient-to-br from-green-500 via-yellow-50 to-green-500  animate-gradient-x shadow-lg text-black border-b-4 opacity-100 border-gray-600">
            <div class="container max-w-6xl mx-auto flex items-center px-2 py-8">

                <div class="w-full mx-auto flex flex-wrap items-center">
                    <div class="flex w-full md:w-1/2 justify-center md:justify-start text-white font-extrabold">
                        <a class="text-gray-900 no-underline hover:text-gray-900 hover:no-underline" href="#">
                            <span class="text-base">Blog</span>
                        </a>
                    </div>
                    <div class="flex w-full pt-2 content-center justify-between md:w-1/2 md:justify-end">
                        <ul class="list-reset flex justify-center flex-1 md:flex-none items-center">
                            <li>
                                <a class="inline-block py-2 px-3 no-underline" href="index.html">HOME</a>
                            </li>
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                            </li>
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                            </li>
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-200 hover:underline py-2 px-3" href="#">link</a>
                            </li>
                        </ul>
                    </div>
                </div>



            </div>
        </footer>
    </div>
</template>

<script>
import BreezeApplicationLogo from "@/Components/ApplicationLogo";
import BreezeDropdown from "@/Components/Dropdown";
import BreezeDropdownLink from "@/Components/DropdownLink";
import BreezeNavLink from "@/Components/NavLink";
import BreezeResponsiveNavLink from "@/Components/ResponsiveNavLink";
import Chat from "@/Pages/Support/chat";

export default {
    props: ['user', 'auth', 'online', 'offline', 'menu_permission'],
    components: {
        Chat,
        BreezeApplicationLogo,
        BreezeDropdown,
        BreezeDropdownLink,
        BreezeNavLink,
        BreezeResponsiveNavLink,
    },
    data() {
        return {
            showingNavigationDropdown: false,
            onlineFriends: [],
            offlineFriends: []
        };
    },
    methods: {
        refreshClient(){
        },
        addOnlineFriend(user){
            let app = this.$page.props
            if(this.onlineFriends.indexOf(user) < 0 && app.user.id != user.id){
                this.onlineFriends.pushIfNotExist(user, function (e) {
                    return e.id === user.id
                })
            }
            this.$store.dispatch('addOnlineFriends', user)
        },
        removeOfflineFriend(offlineUser){
            this.onlineFriends = this.onlineFriends.filter(user => {
                return user.id != offlineUser.id;
            })
            this.$store.dispatch('removeOfflineFriend', offlineUser)
        },
        addOfflineFriend(user){
            let app = this.$page.props
            if(this.offlineFriends.indexOf(user) < 0 && app.user.id != user.id){
                this.offlineFriends.pushIfNotExist(user, function (e) {
                    return e.id === user.id
                })
            }
        },
        removeOnlineFriend(onlineUser){
            this.offlineFriends = this.offlineFriends.filter(user => {
                return user.id != onlineUser.id;
            })
        },
        convertProxyObjectToPojo(proxyObj) {
            return _.cloneDeep(proxyObj);
        },
        logout(){
            this.removeOnlineFriend(this.user)
        }
    },
    created() {
        let app = this;
        app.channel
            .here(users => {
                let withoutMyself = users.filter(user => {
                    return app.$page.props.user.id !== user.id
                })
                this.$store.dispatch('onlineFriendsAsync', withoutMyself)
            })
            .joining(user => {
                axios.put(route('online', user.id))
            })
            .leaving(user => {
                axios.put(route('offline', user.id))
            })
            .listen("OnlineEvent", e => {
                const user = this.convertProxyObjectToPojo(e.user)
                app.addOnlineFriend(user);
                app.removeOnlineFriend(user);
                if (user.id != this.$page.props.user.id){
                    this.$store.dispatch('addOnlineFriends', user)
                }
            })
            .listen('OfflineEvent', e => {
                const user = this.convertProxyObjectToPojo(e.user)
                app.removeOfflineFriend(user);
                app.addOfflineFriend(user)
                this.$store.dispatch('removeOfflineFriend', user)
            });
    },
    computed:{
        channel () {
            return window.Echo.join("user_online_status");
        },
        connection () {
            return window.Echo.connector.pusher.connection;
        },
        onlineFriends(){
            return this.onlineFriends = [...new Set(this.onlineFriends)]
        }
    },
    mounted () {
        let app = this.$page.props
        this.onlineFriends = app.online.filter(user => {
            return user.id != app.user.id;
        })
        this.offlineFriends = app.offline.filter(user => {
            return user.id != app.user.id;
        })
    },
    beforeDestroy () {
        window.Echo.leave("user_online_status");
    },
};
</script>
