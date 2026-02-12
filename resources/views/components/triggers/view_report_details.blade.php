<div class="addvisitor" id="viewVisitorModal" style="display: none;">
    <div class="addvisitormodal">
        <div class="panel">
            {{-- header --}}
            <div class="header fs-4">Visitor Details</div>
            <div class="subheader mb-3">Viewing recorded visitor information</div>
            <button type="button" class="btn-close" aria-label="Close" id="closeViewModal" data-bs-dismiss="modal"></button>
            <div class="form">
                <div class="details">
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="view_id_number" class="form-control" placeholder=" " >
                        <label for="id_number">ID Number</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" id="view_type" class="form-control" placeholder=" " readonly>
                        <label>Visitor Type</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="view_first_name" class="form-control" placeholder=" " >
                        <label for="id_number">first name</label>
                    </div> 
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="view_middle_name" class="form-control" placeholder=" " >
                        <label for="id_number">middle name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="view_last_name" class="form-control" placeholder=" " >
                        <label for="id_number">last name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="id_number" id="view_phone_number" class="form-control" placeholder=" " >
                        <label for="id_number">contact number</label>
                    </div>
                    <div class="input-holder floating w-100">
                        <textarea name="address" id="view_address" class="form-control" placeholder=" " rows="3"></textarea>
                        <label for="id_number">Address</label>
                    </div>
                </div>
                <div class="capture">
                    <div class="header">Visitor Image</div>
                    <div class="imgholder" id="view_image_holder" >No Image</div>
                </div>
            </div>
            <div class="panel-buttons">
                <button type="button" id="closeViewBtn" class="clear">Close</button>
            </div>
        </div>
    </div>
 </div>

@vite('resources/js/visitors.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 