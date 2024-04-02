require("./bootstrap");

// Import modules...
import { createApp, h } from "vue";
import {createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import VuePictureSwipe from 'vue-picture-swipe';
import VueHtmlToPaper from './Plugins/Print/VueHtmlToPaper'
import { store } from "@/Store";
import VueConfirmPlugin from "v3confirm";
import print from 'vue3-print-nb'


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

function ordinal_suffix_of(i) {
    const j = i % 10,
        k = i % 100;
    if (j === 1 && k !== 11) {
        return i + "st";
    }
    if (j === 2 && k !== 12) {
        return i + "nd";
    }
    if (j === 3 && k !== 13) {
        return i + "rd";
    }
    return i + "th";
}

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Madrash & Four Year Diploma Program by IsDB-BISEW';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .mixin({
                methods: {
                    route,
                    GET,
                    ordinal_suffix_of
                },
            })
            .use(plugin)
            .use(print)
            .use(store)
            .use(VueHtmlToPaper)
            .use(VueConfirmPlugin, {
                root: '#confirm',
                yesText: 'Yes',
                noText: 'No',
            })
            .component("font-awesome-icon", FontAwesomeIcon)
            .component('vue-picture-swipe', VuePictureSwipe)
            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#29d',
    },
});
