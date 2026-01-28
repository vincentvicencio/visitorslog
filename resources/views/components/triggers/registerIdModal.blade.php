<!-- Modal -->
<div class="modal fade" id="registerIDModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="textInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">


            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="textInputModalLabel">Register Visitor ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="textInputForm">
                    <div class="mb-3">
                        <label for="visitorType" class="form-label">Visitor Type</label>
                        <select name="visitorType" required class="form-control" id="visitortype">
                            <option value="" disabled selected>Select Visitor Type</option>
                            @foreach ($visitorTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="visitorID" class="form-label">Visitor ID</label>
                        <input type="text" class="form-control" id="visitorID" name="visitorID"
                            placeholder="Enter Visitor ID" autocomplete="off" required>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> --}}
                <button type="button" class="btn btn-primary" id="registerIDSubmit">Submit</button>
            </div>

        </div>
    </div>
</div>


