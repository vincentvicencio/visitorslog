import { Modal } from 'bootstrap';
import Triggers from './common/triggers.js';
import Container from './common/container.js';

$(document).ready(function () {

    $(document).on('click', '#timeoutBtn', function () {
        let Id = $(this).data('id');

        // if (!Id) return;
        Triggers.showNotification(
            '#notificationContainer',
            'Time Out',
            'Are you sure you want to time out this visitor?',
            Id
        );

        // $.ajax({
        //     url: "/visitor/timeout",
        //     type: "POST",
        //     data: {
        //         id: Id,
        //         _token: $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function (response) {
        //         Triggers.showToast(response.message);
        //         setTimeout(() => {
        //             $(location.reload()).fadeOut('slow');
        //         }, 2000);
        //     },
        //     error: function (xhr) {
        //         Triggers.showToast(xhr.responseJSON?.message ?? 'TimeOut failed.');
        //     }
        // });
    });

    $(document).on('click', '#btn_ok', function () {

        let Id = document.getElementById('record_id').value;
        timeoutVisitor(Id);
    });

    $(document).on('click', '#addBtn', function () {

        Container.showModal('#addVisitorModal');
    });
});

function timeoutVisitor(Id) {
    $.ajax({
        url: "/visitor/timeout",
        type: "POST",
        data: {
            id: Id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            Triggers.showToast(response.message);
            setTimeout(() => {
                $(location.reload()).fadeOut('slow');
            }, 2000);
        },
        error: function (xhr) {
            Triggers.showToast(xhr.responseJSON?.message ?? 'TimeOut failed.');
        }
    });
}



const imageModal = new Modal(document.getElementById('imageModal'));

$(document).on('click', '#viewImageBtn', function () {
    const imageUrl = $(this).data('image');

    $('#modalImage').attr('src', imageUrl);
    imageModal.show();
});

