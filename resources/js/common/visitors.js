import container from "./container";

class VisitorLogs{

    constructor(){
        this.modal = "#add_visitor";
    }

    async onLoadPage(){
        this.initializePage()
    }

    async initializePage(){
        const self = this
        container.showModal(self.modal);
    }

}

export default new VisitorLogs();