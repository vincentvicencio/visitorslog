class Datatable {
     /**
     * Create Table  = it will pullout all data
     * @param table  = table id/class
     * @param theads = columns (json format)
     * @param url    = route
     * @param tbodies= rows (json format)
     * @param module = method name (e.g window.forecasting  )
     * @param pagination= how many rows will display
     */
    async createTable(table, theads, url, tbodies = "", module, enableSearch = true, pagination = 10) {
        const self = this

        const data = await datahandling.processData(url + 'list', 'GET')

        $(table).DataTable().clear().destroy()
        $(table).DataTable({
            pageLength:     pagination,
            autoWidth:      false,
            scrollX:        true,
            scrollCollapse: true,
            searching:      enableSearch,
            responsive:     false,
            stateSave:      true,
            paging:         true,
            ordering:       true,
            info:           true,
            pagingType: 'simple',
            language: {
                'paginate': {
                    next:     '<span aria-hidden="true">&gt;</span>',
                    previous: '<span aria-hidden="true">&lt;</span>'
                },
                lengthMenu: "Rows:  _MENU_",
                info:       "_START_ to _END_ of _TOTAL_"
            },
            dom:        '<"top">rt<"bottom"pi><"clear">',
            data:       data, 
            columns:    theads,
            columnDefs: tbodies,
            drawCallback: function () {
                component.initializeButtons(table, url, module)
            }
        })

        // $('.dt-scroll-head').remove();
    }

    /**
     * Create Dynamic Table with Ajax  = pullout record has a limit it uses serverside functionality
     * @param table  = table id/class
     * @param theads = columns (json format)
     * @param url    = route
     * @param tbodies= rows (json format)
     * @param module = method name (e.g window.forecasting  )
     * @param pagination= how many rows will display
     */
    async createTableAjax(table, theads, url, tbodies = "", module, pagination = 10, data = {}, enableSearch = true) {
        const self = this

        $(table).DataTable().clear().destroy()
        $(table).DataTable({
            pageLength:     pagination,
            autoWidth:      false,
            scrollX:        true,
            scrollCollapse: true,
            processing:     true,
            serverSide:     true,
            stateSave:      true,
            searching:      enableSearch,
            dom:            '<"top">rt<"bottom"ip><"clear">',
            search:         { return: true },
            stateLoadParams: function (settings, data) {
                data.length = pagination
            },
            ajax: {
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: window.location.origin + url + 'list',
                type: "POST",
                data: function (d) {
                    d.search = $("#typeSearch").val()
                    $.extend(d, data);
                    if (!d.start) d.start = 0;
                },
                beforeSend: function () { },
                complete:   function (data) { },
                error:      function (error) { }
            },
            language: {
                paginate: {
                    next: '&gt;',
                    previous: '&lt;'
                    // next: '<span aria-hidden="true">&gt;</span>',
                    // previous: '<span aria-hidden="true">&lt;</span>'
                },
                lengthMenu: "_MENU_",
                search: ""
            },
            columns: theads,
            columnDefs: tbodies,
            initComplete: function () {
            // Make bottom row flex container
                const $bottom = $(this.api().table().container()).find('div.bottom');
                $bottom.css({
<<<<<<< HEAD
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between'
=======
                    display:        'flex',
                    alignItems:     'center',
                    justifyContent: 'space-between' // info left, pagination right
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
                });
            },
            drawCallback: function () { component.initializeButtons(table, url,module) }
        });

       // $('.dt-scroll-head').remove();
    }
    
    /**
     * Create Designed Dynamic Table with Ajax  = pullout record has a limit it uses serverside functionality
     * @param table  = table id/class
     * @param theads = columns (json format)
     * @param url    = route
     * @param tbodies= rows (json format)
     * @param module = method name (e.g window.forecasting  )
     * @param pagination= how many rows will display
     */
    async createDesignedTableAjax(table, theads, url, tbodies = "", module = "", pagination = 10, data = {}, searchPlaceholder = "", enableSearch = true, useModal = true) {
        const self = this
        
        $(table).DataTable().clear().destroy()
        $(table).DataTable({
            pageLength      :     pagination,
            autoWidth       :     false,
            scrollX         :     true,
            scrollCollapse  :     true,
            processing      :     true,
            serverSide      :     true,
            stateSave       :     true,
            searching       :     enableSearch,
            search          :     { return: true },
            pagingType      :     'simple',
            language: {
                'paginate': {
                    next:     '<span aria-hidden="true">&gt;</span>',
                    previous: '<span aria-hidden="true">&lt;</span>'
                },
                lengthMenu: "Rows:  _MENU_",
                info:       "_START_ to _END_ of _TOTAL_"

            },
            dom: '<"top">rt<"bottom"ip><"clear">',
            ajax: {
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: window.location.origin + url + 'list',
                type: "POST",
                data: function (d) {
                    d.search = $("input[type='search']").val()
                    $.extend(d, data);
                    if (!d.start) d.start = 0;
                },
                beforeSend: function () { },
                complete:   function (data) { },
                error:      function (error) { }
            },
            columns:      theads,
            columnDefs:   tbodies,
            drawCallback: function () { component.initializeButtons(table, url, useModal) }
        });

        $('.dt-scroll-head').remove();

    }

    sortmenu(label, tableHeader, allowedHeader){

        let dropdownId = 'dropdown_' + Math.random().toString(36).substr(2, 9);

        let $dropdown  = $('<div>').addClass('dropdown')

        let $toggle    = $('<i>', {
            'class'         : 'bi bi-filter-left dropdown-toggle menu-list',
            'role'          : 'button',
            'id'            : dropdownId,
            'data-bs-toggle': 'dropdown',
            'aria-expanded' : 'false'
        }).html(`<span class="ms-1">${label}</span>`)


        let $ul = $('<ul>', {
            'class'             : 'dropdown-menu dropdown-menu-end',
            'aria-labelledby'   : dropdownId
        })

            
        $('<li>').addClass('p-2').html('<small>Sort By</small>').appendTo($ul)
        $('<li>').html('<hr class="dropdown-divider">').appendTo($ul)

        $.each(tableHeader, function(index, value){
            if ($.inArray(index, allowedHeader) !== -1)                 {                
                $('<li>').addClass('p-1').html(`<span class="ms-3"><small>${value.label}</small></span>`).appendTo($ul)
                $('<li>').html('<hr class="dropdown-divider">').appendTo($ul)
            }
        })

        $dropdown.append($toggle).append($ul)

    return $dropdown
    }
}

export default new Datatable;