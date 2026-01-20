class Datahandling {

    /**
     *
     * @param url    = route
     * @param method = POST/GET
     * @param data   = json format
     * @returns
     */
    async processData(url, method, data) {
        const response = await $.ajax({
            url: window.location.origin + url,
            type: method,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: data,
            dataType: 'json',
        });
        return response;
    }

    /**
     *
     * @param url      = route
     * @param table    = table id
     * @param formid   = form id
     * @param formdata = formdata // new FormData($(this)[0])
     */
    async saveForm(url, table, formid, formdata, config = []) {
        const self = this

        const $submitBtn = $(formid).find('button[type="submit"]');
        const $spinner = $submitBtn.find('.spinner-border');
        const $btnText = $submitBtn.find('.btn-text');

        $submitBtn.prop('disabled', true);

        if ($spinner.length > 0) $spinner.removeClass('d-none');
        if ($btnText.length > 0) $btnText.addClass('d-none');

        $.ajax({
            url: window.location.origin + url,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            data: formdata,
            processData: false,
            contentType: false,
            success: function (data) {
                $submitBtn.prop('disabled', false);
                if ($spinner.length > 0) $spinner.addClass('d-none');
                if ($btnText.length > 0) $btnText.removeClass('d-none');

                var errors = data.errors;
                $.each(errors, function (index, value) {
                    triggers.showError(formid, index, value)
                })

                if (!errors) {

                    triggers.showToast(data);

                    if (config.module) datatable.createTable(config.table, config.columns, config.url, config.targets)
                    else $(table).DataTable().ajax.reload()

                    setTimeout(() => {
                        if (config.module) self.clearForm(formid)
                        $(".btn-close").trigger('click')
                    }, 1000);
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false);
                if ($spinner.length > 0) $spinner.addClass('d-none');
                if ($btnText.length > 0) $btnText.removeClass('d-none');

                alert("Oooo Something went wrong.");
            }

        });
    }

    // Clear Form
    async clearForm(form) {

        $(form).find('input, select, textarea').not('input[type="checkbox"]').val("").removeClass('error-input').trigger('change');

        $(form).find('select').val([]).trigger('change');

        $(form).find('input[type="checkbox"]').prop('checked', false);

        // Reset success and error messages
        $(".alert-success").text("").hide();
        $(".alert-danger").hide();

        // Hide error spans
        $(".error-span").addClass('d-none');
    }

    async ajaxCall(type = "GET", url, formdata = null, button = null) {
        let originalButtonContent = '';
        if (button) {
            originalButtonContent = $(button).html();
            $(button).prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');
        }

        let ajaxOptions = {
            url: window.location.origin + url,
            type: type,
            data: formdata,
            success: function (data) {
                return data;
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
            },
            complete: function () {
                if (button) {
                    $(button).prop("disabled", false).html(originalButtonContent);
                }
            }
        };

        // Add CSRF token in headers if the request is POST
        if (type === "POST") {
            ajaxOptions.headers = {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            };

            // Check if formdata is an instance of FormData
            if (formdata instanceof FormData) {
                ajaxOptions.processData = false;
                ajaxOptions.contentType = false;
            } else {
                ajaxOptions.contentType = "application/json; charset=UTF-8";
                ajaxOptions.data = JSON.stringify(formdata);
            }
        }

        return await $.ajax(ajaxOptions);
    }

    //Get Fields
    async setupFormFields(form, container, container_id, url, fields = "") {
        const self = this

        datastorage.removeItem(self.editFormParam)

        fields = fields ? fields : self.getFields(form);

        datastorage.setItem(self.editFormParam, JSON.stringify({
            'defaultFields': fields,
            'layout': container,
            'container_id': container_id,
            'form': form,
            'url': url
        }))
    }

    async getFields(formId) {
        let fields = {};

        $(formId + " :input:not([type*='hidden'])").each(function () {
            let name = $(this).attr("name");

            if (!name) return;

            name = name.replace(/\[.*?\]/g, "");

            let title = name.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());

            fields[name] = {
                in_form: 1,
                title: title,
            };

        });

        fields['id'] = {
            in_form: 1,
            title: 'ID'
        };

        return fields;
    }

    // Random Number
    async getRandomSeed() {
        return Math.floor(Math.random() * 1000);
    }

    /**
     * Function to filter the element based on the search term
     *
     * @param {string} search_id - The id of the search input
     * @param {string} filter_container - The container to be filtered
     */
    async searchProcess(search_id, filter_container) {

        $(search_id).on('keyup', function () {
            let searchTerm = $(this).val().toLowerCase()

            $(filter_container).filter(function () {
                let text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchTerm) > -1)
            })
        })
    }

}

export default new Datahandling;
