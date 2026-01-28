<div class="addvisitor modal fade" id="addVisitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="panel modal-content">
            <div class="modal-header"></div>
                <div class="header fs-4">Add Visitor
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form" id="addVisitorForm">
                <div class="details">
                    <div class="input-holder floating">
                        <input type="hidden" name="id" id="id" class="form-control" placeholder=" " >
                        <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " value="{{ $visitor->id_number }}">
                        <label for="id_number">ID Number</label>
                    </div>
                    <div class="input-holder floating">
                        <select name="visitor_type" id="visitor_type" class="form-control" required {{ $visitor->visitor_type }}>
                            <option value="" disabled selected>Select Visitor Type</option> <!-- Empty option for floating effect -->
                            @foreach ($visitorTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <label for="visitor_type">Visitor Type</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " {{ $visitor->first_name }}>
                        <label for="first_name">first name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" " {{ $visitor->middle_name }}>
                        <label for="middle_name">middle name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " {{ $visitor->last_name }}>
                        <label for="last_name">last name</label>
                    </div>
                    <div class="input-holder floating">
                        <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder=" " {{ $visitor->phone_number }}>
                        <label for="contact_number">contact number</label>
                    </div>
                    <div class="input-holder floating w-100">
                        <textarea name="address" id="address" class="form-control" placeholder=" " rows="3" {{ $visitor->location }}></textarea>
                        <label for="address">Address</label>
                    </div>
                </div>
                <div class="capture">
                    <div class="header">Capture Image</div>
                    <div class="imgholder" {{ $visitor->image_path }}>No Image</div>
                    <button type="button" class="capture-button">capture</button>
                </div>
            </form>
            <div class="panel-buttons">
                @if($visitor->id != null)
                    <button type="button" class="save">save</button>
                    <button type="button" class="clear">clear</button>
                @endif
            </div>
        </div>
    </div>
 </div>


@vite('resources/js/visitors.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 