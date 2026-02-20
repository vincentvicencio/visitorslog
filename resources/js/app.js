import './bootstrap';

import * as bootstrap from 'bootstrap';


import $ from 'jquery';
import select2 from 'select2';

// Important: Initialize the plugin
select2(); 

// Make jQuery available globally
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
