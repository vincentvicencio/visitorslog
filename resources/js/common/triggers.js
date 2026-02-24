class Triggers {

    /**
     *
     * @param msg = notification message
     * @param err = 1=Error, 0/null = Success
     */
    async showToast(msg, err = 0) {
        $('.toast-body strong').text(msg);
        $('.toast').css('z-index', 10000);
        $('.toast').removeClass('bg-danger bg-success');
        $('.toast').addClass(err > 0 ? 'bg-danger' : 'bg-success');
        $('.toast').fadeIn('slow');

        setTimeout(() => {
            $('.toast').fadeOut('slow');
        }, 2000);

    }
    /**
     * @param container_id = modal id
     * @param title    = title
     * @param message  = message
     * @param id       = record id
     */

    async showNotification(container_id, title, message, id) {

        $(".notification-message").empty()
        $(".notification-body").show()
        $("#record_id").val(id)
        $("#notification-title").text(title)
        $("#notification-message").text(message)

        container.showModal(container_id)
    }

    async processButtonOk(url, data) {
        const self = this
        $("#notificationContainer #btn_ok").on('click', async function () {
            const response = await datahandling.processData(url, 'POST', data)

            if (response) {
                self.showToast(response.message)
                setTimeout(() => { location.reload() }, 2000)
            }
        })
    }

    /**
     * Show Error
     * @param index = element id without #
     * @param value = error message
     */
    async showError(formid, index, value) {
        var elem_id = '#' + index;
        $(formid + ' ' + elem_id).addClass('error-input');
        $(formid + ' .error-' + index).removeClass('d-none');
        $(formid + ' .error-' + index).html('<i class="bi bi-exclamation-circle-fill"></i> ' + value);
    }

    /**
     * Hide Error
     * @param index = element id without #
     */
    async removeError(index) {
        var elem_id = '#' + index;
        $(elem_id).removeClass("error-input");
        $(".error-" + index).empty();
        $(".error-" + index).addClass('d-none');
    }

    // Remove All Errors
    async removeAllError() {
        $('.error-span').empty();
    }
    /**
     * Remove Error on input = auto remove when has input value
     * @param formid = form id
     */
    async removeErrorOnInput(formid) {
        let self = this;
        $(formid).find(':input').on('input', function (e) {
            var index = $(this).attr('id');
            if ($(this).val() != '' && index) self.removeError(index);
        });
    }
}

export default new Triggers();