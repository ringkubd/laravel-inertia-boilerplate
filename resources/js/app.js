require("./bootstrap");

// Import modules...
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Select2 from 'vue3-select2-component';
import VuePictureSwipe from 'vue-picture-swipe';
import VueHtmlToPaper from './Plugins/Print/VueHtmlToPaper'
import { store } from "@/Store";

const el = document.getElementById("app");

createInertiaApp({
    title: title => `${title} - Four Year Diploma Project Management`,
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, app, props, plugin }) {
        createApp({
            render: () => h(app, props),
        })
        .mixin({
            methods: {
                route,
                GET
            },
        })
        .use(plugin)
        .use(store)
        .use(VueHtmlToPaper)
        .component("font-awesome-icon", FontAwesomeIcon)
        .component("Select2", Select2)
        .component('vue-picture-swipe', VuePictureSwipe)
        .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
