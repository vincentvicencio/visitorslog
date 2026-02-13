import './bootstrap';

import * as bootstrap from 'bootstrap';


import $ from 'jquery';
import select2 from 'select2';

// Important: Initialize the plugin
select2(); 

// Make jQuery global so the plugin can find it in the window scope
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
