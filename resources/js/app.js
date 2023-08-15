import '../css/app.css';

import {createInertiaApp} from "@inertiajs/inertia-svelte";
import {InertiaProgress} from "@inertiajs/progress";

const appName = window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
    title: title => `${title} - ${appName}`,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', {eager: true})
        return pages[`./Pages/${name}.svelte`]
    },
    setup({el, App, props}) {
        new App({target: el, props});
    },
    progress: {
        // The delay after which the progress bar will appear, in milliseconds...
        delay: 250,

        // The color of the progress bar...
        color: '#29d',

        // Whether to include the default NProgress styles...
        includeCSS: true,

        // Whether the NProgress spinner will be shown...
        showSpinner: false,
    },
});

InertiaProgress.init({color: "#4B5563"});
