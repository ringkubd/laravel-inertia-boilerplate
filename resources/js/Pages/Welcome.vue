<template>
    <Head>
        <title>Four Year Diploma | IsDB-BISEW</title>
    </Head>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-brand-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('dashboard')" class="flex items-center">
                            <ApplicationLogo class="h-10 w-auto" />
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-white hover:text-brand-200 transition">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm text-white hover:text-brand-200 transition">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="text-sm bg-white text-brand-800 px-4 py-2 rounded-md font-medium hover:bg-brand-50 transition">
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="flex-1 min-h-[50vh] flex items-center justify-center">
                    <div class="text-center lg:text-left">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                            Four Year Diploma Program
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Islamic Development Bank - Bangladesh Islamic Solidarity Educational Wakf
                        </p>
                    </div>
                </div>
                <div class="w-full lg:w-80">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 text-center">App Download</h4>
                        <a class="flex justify-center" href='https://play.google.com/store/apps/details?id=com.ringkubd.diploma_student&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'>
                            <img alt='Get it on Google Play' class="h-16 w-auto" src='https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png'/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo";

export default {
    components: {ApplicationLogo},
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
        applications: Object
    },
}
</script>
