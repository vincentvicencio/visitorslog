import './bootstrap';
import './dashboard';

import * as bootstrap from 'bootstrap';


import $ from 'jquery';
import select2 from 'select2';

// Important: Initialize the plugin
select2(); 

// Make jQuery available globally
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

const SESSION_EXPIRED_CODES = [401, 419];

function showSessionExpiredPrompt() {
	const modalElement = document.getElementById('sessionExpiredModal');

	if (!modalElement || typeof window.bootstrap?.Modal === 'undefined') {
		window.location.reload();
		return;
	}

	const instance = window.bootstrap.Modal.getOrCreateInstance(modalElement, {
		backdrop: 'static',
		keyboard: false,
	});

	instance.show();
}

window.showSessionExpiredPrompt = showSessionExpiredPrompt;

if (!window.__sessionExpiredInterceptorBound && window.axios) {
	window.__sessionExpiredInterceptorBound = true;

	window.axios.interceptors.response.use(
		(response) => response,
		(error) => {
			const status = error?.response?.status;

			if (SESSION_EXPIRED_CODES.includes(status)) {
				showSessionExpiredPrompt();
			}

			return Promise.reject(error);
		}
	);
}

if (!window.__sessionExpiredJqueryBound && window.$) {
	window.__sessionExpiredJqueryBound = true;

	window.$(document).ajaxError((_event, jqXHR) => {
		const status = jqXHR?.status || 0;

		if (SESSION_EXPIRED_CODES.includes(status)) {
			showSessionExpiredPrompt();
		}
	});
}

if (!window.__sessionExpiredFetchBound && typeof window.fetch === 'function') {
	window.__sessionExpiredFetchBound = true;

	const nativeFetch = window.fetch.bind(window);

	window.fetch = async (...args) => {
		const response = await nativeFetch(...args);

		if (SESSION_EXPIRED_CODES.includes(response.status)) {
			showSessionExpiredPrompt();
		}

		return response;
	};
}

document.addEventListener('DOMContentLoaded', () => {
	const refreshButton = document.getElementById('sessionRefreshButton');

	if (refreshButton) {
		refreshButton.addEventListener('click', () => {
			window.location.reload();
		});
	}
});
