import { Modal } from 'bootstrap';

class Container {

    /**
     * Show Modal
     * @param modal_id = modal id
     */
    async showModal(modal_id) {
        const modalElement = document.querySelector(modal_id);
        
        // Get or create Bootstrap modal instance
        let modalInstance  = Modal.getInstance(modalElement);
        if (!modalInstance) {
            modalInstance  = new Modal(modalElement);
        }

        
        // Show modal
        modalInstance.show();
    }

 
    /**
     * Hide Modal
     * @param modal_id = modal id
     */
    async hideModal(modal_id) {

        var modalElement  = document.querySelector(modal_id);
        var modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
        }

        modalInstance.hide();
    }

    /**
     * Show offcanvas
     * @param offcanvas_id = offcanvas id
     */
    async showOffcanvas(offcanvas_id) {
        var offcanvasElement  = document.querySelector(offcanvas_id);
        var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);

        if (!offcanvasInstance) {
            offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
        }

        offcanvasInstance.show()
    }

    async showFormPane(formPane, contentPane){
        $(formPane).css({ right: "0", width: "49%" })
        $(contentPane).css({ width: "50%" }).show()

        $(`${formPane} .btn-maximize i`)
            .removeClass("bi-arrows-angle-contract")
            .addClass("bi-arrows-angle-expand")

        

        return false // isMaximized
    }

    async closeFormPane(formPane, contentPane){
        
        $(formPane).css({ right: "-50%", width: "50%" })
        $(contentPane).css({ width: "100%", transform : "translateX(0)" }).show()

        $(`${formPane} .btn-maximize i`)
                .removeClass("bi-arrows-angle-contract")
                .addClass("bi-arrows-angle-expand")

        return false // isMaximized
    }

    async maximizeFormPane(formPane, contentPane, isMaximized){

        if (!isMaximized) {      
            $(formPane).css({ width: "100%", right: "0", 'margin-left': '1rem' })
            $(this).find("i").removeClass("bi-arrows-angle-expand").addClass("bi-arrows-angle-contract")           
            isMaximized = true

        } else {
            $(formPane).css({ width: "49%", right: "0" })
            $(this).find("i").removeClass("bi-arrows-angle-contract").addClass("bi-arrows-angle-expand")
            
            $(contentPane).css({ width: "50%" }).show()

            isMaximized = false
        }

        return isMaximized
    }
}

export default new Container();