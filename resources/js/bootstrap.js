// ------------------------------------
// Axios (Laravel default)
// ------------------------------------
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ------------------------------------
// jQuery (global)
// ------------------------------------
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// ------------------------------------
// Bootstrap Sortable
// ------------------------------------
// import Sortable from 'sortablejs';
// window.Sortable = Sortable;


// ------------------------------------
// Bootstrap JS
// ------------------------------------
import 'bootstrap';

// ------------------------------------
// DataTables (with Bootstrap 5 styling)
// ------------------------------------
import 'datatables.net';
import 'datatables.net-bs5';

// ------------------------------------
// Select2
// ------------------------------------
import 'select2';


// ------------------------------------
// Common JS
// ------------------------------------
import Component from "./common/component";
window.component = Component;

import Container from "./common/container";
window.container = Container;

import Settable from "./common/settable";
window.settable = Settable;

import Datahandling from "./common/datahandling";
window.datahandling = Datahandling;

import Triggers from "./common/triggers";
window.triggers = Triggers;


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
