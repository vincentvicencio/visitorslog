import container from './common/container';
import datahandling from './common/datahandling';
import triggers from './common/triggers';
import settable from './common/settable';
import component from './common/component';
import $ from 'jquery';


class VisitorTypeTable {
    constructor() {
        this.defaultFields  = []
        // first parameter of your route
        this.url            = "/visitortype/"
        // id name of your table listing in user
        this.table          = "#visitorsTable"
        // module
        this.module         = "visitortype"
        // form id
        this.form           = "#textInputForm"
        // offCanvas
        this.modal          = "#textInputModal"
        // add user form id
        this.formid         = "#"  

    }


    async onLoadPage(){
        // this.initializePage();
        this.list();
    }

    // async initializePage(){
    //     const self = this

    //     //Open Modal
    //     $("#addBtn").on('click', function(){ 
    //         // console.log('clicked')   
    //         // Clear Form  
    //         datahandling.clearForm(self.form) 
    //         // $('#textInputModalLabel').text('Register Visitor Type');
    //         // show Canvas Form
    //         container.showModal(self.modal)

    //         // openTextInputModal('0', 'name')
    //     })

    //     $(this.form).on('submit', async function(e){
    //         e.preventDefault()
    //         await datahandling.saveForm('/visitortype/save', self.table, self.form, new FormData(this))
    //     })
        
    // }


    // async onLoadForm(record_id) {
    //     const self = this;

    //     const url = self.url+'search';
    //     const response = await datahandling.processData(
    //         url,
    //         'POST',
    //         { id: record_id }
    //     );

    //     $("#item_id").val(record_id);
    //     $("#requestItemName").val(response.record.name);

    //     $('#textInputModalLabel').text('Edit Visitor Type');

    //     container.showModal(self.modal);
    // }


    async list() {
        const self = this;

        const tableHeader = [
            { id: "name",       label: "Name" },
            { id: "created_by",       label: "Created By" },
            { id: "updated_by",      label: "Updated By" },
            { id: "created_at",   label: "Created Date" },
            { id: "action",         label: "Action" },
        ];

        const columns = tableHeader.map(col => ({
            data: col.id, 
            title: col.label,
            width: 'auto'
        }));

        const columnDefs = [
            { targets: [0, 1, 2, 3], orderable: false }
        ]; 

        settable.createTableAjax(
            self.table,
            columns,
            self.url,
            columnDefs,
            // 10,          // ✅ pagination
            // {}           // ✅ data
        );

        const tableApi = $(self.table).DataTable();
        $('input[type="search"]').off('keyup').on('keyup', function() {
            tableApi.search(this.value).draw();
        });

         setTimeout(() => {
            const searchInput = document.getElementById('dt-search-0');             
                if (searchInput) {
                    searchInput.setAttribute('placeholder', 'Search here...');
                }
            }, 100);

    }


}
const visitorsType = new VisitorTypeTable();
visitorsType.onLoadPage();

export default visitorsType;
