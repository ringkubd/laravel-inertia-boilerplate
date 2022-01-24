<template>
    <Head>
        <title>{{title}}</title>
    </Head>
    <div class="min-h-screen min-w-screen bg-gray-100">
        <div>
            <header id="top" class="w-full flex flex-col sm:relative bg-white pin-t pin-r pin-l">
                <nav id="site-menu" class="flex flex-col sm:flex-row w-full justify-between items-center px-4 sm:px-6 py-1 bg-[#002147] shadow-lg mb-2 sm:shadow-none border-t-4 border-red-900 sticky top-0 z-50">
                    <div class="w-full sm:w-auto self-start sm:self-center flex flex-row sm:flex-none flex-no-wrap justify-between items-center">
                        <inertia-link href="/">
                            <breeze-application-logo class="w-20 h-20 fill-current text-gray-500" />
                        </inertia-link>
                        <inertia-link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-gray-700 underline ml-4">
                            Dashboard
                        </inertia-link>
                        <button id="menuBtn" class="hamburger block sm:hidden focus:outline-none" type="button" @click="navToggle">
                            <span class="hamburger__top-bun"></span><span class="hamburger__bottom-bun"></span>
                        </button>
                    </div>
                    <div id="menu" class="w-full sm:w-auto self-end sm:self-center sm:flex flex-col sm:flex-row items-center h-full py-1 pb-4 sm:py-0 sm:pb-0 hidden">
                        <a v-for="(url, menu) in $page.props.public_menus" class="text-dark font-bold hover:text-red text-lg w-full no-underline sm:w-auto sm:pr-4 py-2 sm:py-1 sm:pt-2" :href="url" target="_blank">{{menu}}</a>
                    </div>
                </nav>
            </header>
            <main class="w-full">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import BreezeApplicationLogo from '@/Components/ApplicationLogo'

export default {
    components: {
        BreezeApplicationLogo,
    },
    props: [],
    methods: {
        navToggle() {
            var btn = document.getElementById('menuBtn');
            var nav = document.getElementById('menu');

            btn.classList.toggle('open');
            nav.classList.toggle('flex');
            nav.classList.toggle('hidden');
        }
    },
    mounted() {
        const nav = document.getElementById('site-menu');
        const header = document.getElementById('top');

        window.addEventListener('scroll', function() {
            if (window.scrollY >=400) { // adjust this value based on site structure and header image height
                nav.classList.add('nav-sticky');
                header.classList.add('pt-scroll');
            } else {
                nav.classList.remove('nav-sticky');
                header.classList.remove('pt-scroll');
            }
        });
    }
}

</script>
<style>
/* custom non-Tailwind CSS */

@media (max-width: 576px) {
    .content {
        padding-top: 51px;
    }
}

@media (min-width: 577px) {
    .pt-scroll {
        padding-top: 51px;
    }

    .nav-sticky {
        position: fixed!important;
        min-width: 100%;
        top: 0;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .1);
        transition: all .25s ease-in;
        z-index: 1;
    }
}

/* HAMBURGER MENU */

.hamburger {
    cursor: pointer;
    width: 48px;
    height: 48px;
    transition: all 0.25s;
}

.hamburger__top-bun,
.hamburger__bottom-bun {
    content: '';
    position: absolute;
    width: 24px;
    height: 2px;
    background: #000;
    transform: rotate(0);
    transition: all 0.5s;
}

.hamburger:hover [class*="-bun"] {
    background: #333;
}

.hamburger__top-bun {
    transform: translateY(-5px);
}

.hamburger__bottom-bun {
    transform: translateY(3px);
}

.open {
    transform: rotate(90deg);
    transform: translateY(-1px);
}

.open .hamburger__top-bun {
    transform:
        rotate(45deg)
        translateY(0px);
}

.open .hamburger__bottom-bun {
    transform:
        rotate(-45deg)
        translateY(0px);
}
</style>
