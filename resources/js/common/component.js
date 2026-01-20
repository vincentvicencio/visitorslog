
class Component {

    createSortable(selector, options = {}, cursor = 'grab') {
        $(selector).each(function () {
            const $el = $(this)
            $el.data('sortable')?.destroy()
            $el.data('sortable', null)

            const instance = Sortable.create(this, options)
            $el.data('sortable', instance)
        })

        $(selector).find(options.draggable).on('mousedown', function () {
            $(this).css('cursor', 'grabbing')
        })
    }

    // Initialize Button
    initializeButtons(table_name, url, moduleName) {
        const self = this;
        const $table = $(table_name);
        const tableApi = $table.DataTable();


        const modules = import.meta.glob('../administrator/*.js');

        // -------------------------
        // EDIT
        // -------------------------
        $table.off('click', '.btn-edit').on('click', '.btn-edit', async function () {

            const regex = new RegExp(`/${moduleName}\\.js$`)

            const importKey = Object.keys(modules).find(key => regex.test(key))

            const imported = await modules[importKey]()

            const instance = typeof imported.default === 'function'
                ? new imported.default()
                : imported.default


            let record_id = $(this).attr('data-id');
            instance.onLoadForm?.(record_id)
        });

        // -------------------------
        // View
        // -------------------------
        $table.off('click', '.btn-view').on('click', '.btn-view', async function () {

            const regex = new RegExp(`/${moduleName}\\.js$`)

            const importKey = Object.keys(modules).find(key => regex.test(key))


            const imported = await modules[importKey]()

            const instance = typeof imported.default === 'function'
                ? new imported.default()
                : imported.default

            let record_id = $(this).attr('data-id');
            instance.onLoadView?.(record_id)
        });


        // -------------------------
        // DELETE
        // -------------------------
        $table.off('click', '.btn-delete').on('click', '.btn-delete', function () {
            const id = $(this).attr('data-id');
            const details = $(this).attr('data-details');
            const message = `Are you sure you want to delete ${details}'s record?`;
            triggers.showNotification("#notificationContainer", "Delete Notification", message, id);
            triggers.processButtonOk(url + "delete", { id: $("#record_id").val() });
        });


        // -------------------------
        // COPY
        // -------------------------
        $table.off('click', '.btn-copy').on('click', '.btn-copy', function () {
            const id = $(this).attr('data-id');
            const details = $(this).attr('data-details');
            const message = `Are you sure you want to copy ${details}'s record?`;
            triggers.showNotification("#notificationContainer", "Copy Notification", message, id);
            triggers.processButtonOk(url + "copy", { id: $("#record_id").val() });
        });

        // -------------------------
        // GLOBAL SEARCH
        // -------------------------
        $('#global_filter').off('change').on('change', function () {
            tableApi.search($('#global_filter').val(), true, false).draw();
        });

        // -------------------------
        // HEADER CHECK ALL
        // -------------------------
        // Use table header API to get the visible cloned header
        $(tableApi.table().header()).find('#check_all')
            .off('change')
            .on('change', function () {
                const checked = this.checked;
                $table.find('tbody .check').prop('checked', checked);
            });

        // -------------------------
        // ROW CHECKBOXES
        // -------------------------
        $table.off('change', 'tbody .check')
            .on('change', 'tbody .check', function () {
                const total = $table.find('tbody .check').length;
                const checked = $table.find('tbody .check:checked').length;

                // Sync header checkbox
                $(tableApi.table().header()).find('#check_all').prop('checked', total === checked);
            });

        // Unbind previous handlers to prevent duplicates
        $table.off('click', 'tbody tr')
            .on('click', 'tbody tr', function (e) {

                // Ignore clicks on last column (actions)
                if ($(e.target).closest('td').is(':last-child')) return;

                // Ignore clicks on the checkbox itself
                if ($(e.target).is('input:checkbox')) return;

                // Toggle the checkbox in this row
                const $checkbox = $(this).find('input.check');
                $checkbox.prop('checked', !$checkbox.prop('checked'));

                // Update the header "Check All" checkbox
                const total = $table.find('tbody .check').length;
                const checked = $table.find('tbody .check:checked').length;
                $(tableApi.table().header()).find('#check_all').prop('checked', total === checked);
            });

        // -------------------------
        // Optional: Sync on table redraw
        // -------------------------
        tableApi.off('draw').on('draw', function () {
            const total = $table.find('tbody .check').length;
            const checked = $table.find('tbody .check:checked').length;
            $(tableApi.table().header()).find('#check_all').prop('checked', total === checked);
        });
    }

    btnSpinner(btn, spinner, btn_text, show) {
        const $btn = btn;
        const $spinner = $btn.find(spinner);
        const $btnText = $btn.find(btn_text);

        if (show) {
            // Show spinner
            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $btnText.addClass('d-none');
        }
        else {
            // Hide spinner after response
            $btn.prop('disabled', false);
            $spinner.addClass('d-none');
            $btnText.removeClass('d-none');
        }
    }

    createDropdown(url, element_id, data = null, popContainer_id = "") {
        var records = $.ajax({
            url: window.location.origin + url,
            type: "POST",
            async: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: data,
            success: function (data) { return data; },
        }).responseJSON;

        $(element_id).empty();

        popContainer_id ? $(element_id).select2({ data: records, dropdownParent: $(popContainer_id), width: '100%' })
            : $(element_id).select2({ data: records });
    }

    formatDate(datetime) {

        var datetime = new Date(datetime);

        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var day = datetime.getDate();
        var month = months[datetime.getMonth()];
        var year = datetime.getFullYear();
        var hours = datetime.getHours();
        var minutes = datetime.getMinutes();

        minutes = minutes < 10 ? "0" + minutes : minutes;

        hours = hours % 12; // Convert to 12-hour format
        hours = hours ? hours : 12; // Handle midnight case

        return day + " " + month + " " + year + " " + hours + ":" + minutes + " " + (hours >= 12 ? 'PM' : 'AM');
    }

    formatTime(seconds) {
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var remainingSeconds = seconds % 60;

        // Add leading zeros if necessary
        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        remainingSeconds = (remainingSeconds < 10) ? "0" + remainingSeconds : remainingSeconds;

        return hours + ":" + minutes + ":" + remainingSeconds;
    }

    createSearchForm(headers, allowedHeader) {

        // Search Field
        const $searchFields = $("#searchFieldsList").empty()

        // Display Column
        const $displayFields = $("#displayFieldsList").empty()

        // Sort Column
        const $sortable = $("#sortable").empty()

        $.each(headers, function (index, value) {

            const label = value.label
            const id = value.id

            if (index === 0) {
                $('<li>').html('<a class="dropdown-item" href="#" data-field="all">All</a>').appendTo($searchFields);
                $('<li>').html('<hr class="dropdown-divider">').appendTo($searchFields);
            }

            if ($.inArray(index, allowedHeader) !== -1) {
                $('<li>').html(`<a class="dropdown-item" href="#">${label}</a>`).appendTo($searchFields)
                $('<li>').html('<hr class="dropdown-divider">').appendTo($searchFields)
            }

            if (index === 0 || index === headers.length - 1) return;

            let $li = $('<li>', { class: 'list-group-item' })
            let $input = $('<input>', {
                'class': 'form-check-input column_chk me-1',
                'type': 'checkbox',
                'id': `${id}_chk`,
                'data-col-id': id,
                'checked': true
            })
            let $label = $('<label>', {
                'class': 'form-check-label',
                'for': `${id}_chk`,
                'text': label
            })


            $li.append($input).append($label)

            $displayFields.append($li)

            let $sortable_li = $('<li>', { class: 'list-group-item mb-2' })
            let $sortable_icon = $('<i>', { class: 'bi bi-grip-vertical me-3' })
            let $sortable_label = $('<label>', {
                'class': 'form-check-label',
                'for': `${id}_chk`,
                'text': label
            })


            $sortable_li.append($sortable_icon).append($sortable_label)

            $sortable.append($sortable_li)

        })

    }


    createSortable(selector, options = {}, cursor = 'grab') {
        $(selector).each(function () {
            const $el = $(this)
            $el.data('sortable')?.destroy()
            $el.data('sortable', null)

            const instance = Sortable.create(this, options)
            $el.data('sortable', instance)
        })

        $(selector).find(options.draggable).on('mousedown', function () {
            $(this).css('cursor', 'grabbing')
        })
    }

}

export default new Component;
