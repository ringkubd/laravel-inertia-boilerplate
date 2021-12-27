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
import VueConfirmPlugin from "v3confirm";


const el = document.getElementById("app");

Array.prototype.inArray = function(comparer) {
    for(var i=0; i < this.length; i++) {
        if(comparer(this[i])) return true;
    }
    return false;
};
// adds an element to the array if it does not already exist using a comparer
// function
Array.prototype.pushIfNotExist = function(element, comparer) {
    if (!this.inArray(comparer)) {
        this.push(element);
    }
};

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Madrash & Four Year Diploma Program by IsDB-BISEW';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
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
            .use(VueConfirmPlugin, {
                root: '#confirm',
                yesText: 'Yes',
                noText: 'No',
            })
            .component("font-awesome-icon", FontAwesomeIcon)
            .component("Select2", Select2)
            .component('vue-picture-swipe', VuePictureSwipe)
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
