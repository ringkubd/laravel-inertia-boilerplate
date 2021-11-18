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
