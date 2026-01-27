$document.ready(function(){

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': window.Laravel.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    }
});



});