<div class="addvisitor" style="display: none;">
    <div class="addvisitormodal">
        <div class="panel">
            <div class="header fs-4">Add Visitor</div>
            <div class="subheader mb-3">Register and record a new visitor entry</div>
            <button type="button" class="btn-close" aria-label="Close"></button>
            <div class="form">
                <div class="details">
                    <div class="input-holder floating">
                        <input type="hidden" name="id" id="id" class="form-control" placeholder=" " >
                        <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " value="">
                        <label for="id_number">ID Number</label>
                    </div>
                    <div class="input-holder floating">
                        <select name="visitor_type" id="visitor_type" class="form-control" required >
                            <option value="" disabled selected>Select Visitor Type</option> <!-- Empty option for floating effect -->
                            <option value="summit_one">Summit One</option>
                            <option value="facility_center">Facility Center</option>
                            <option value="mezzanine">Mezzanine</option>
                        </select>
                        <label for="visitor_type">Visitor Type</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required>
                        <label for="first_name">first name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" ">
                        <label for="middle_name">middle name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " required>
                        <label for="last_name">last name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder=" " required>
                        <label for="contact_number">contact number</label>
                    </div>
                    <div class="input-holder floating w-100">
                        <textarea name="address" id="address" class="form-control" placeholder=" " rows="3" required></textarea>
                        <label for="address">Address</label>
                    </div>
                </div>
                <div class="capture">
                    <div class="header">Capture Image</div>
                    <div class="imgholder">No Image</div>
                    <button type="button" class="capture-button">capture</button>
                </div>
            </div>
            <div class="panel-buttons">
                <button type="button" class="save">save</button>
                <button type="button" class="clear">clear</button>
            </div>
        </div>
    </div>
 </div>


@vite('resources/js/visitors.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 