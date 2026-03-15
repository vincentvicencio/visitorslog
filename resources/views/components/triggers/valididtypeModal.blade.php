<div class="modal fade" id="valididtypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="textInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="valididtypeModalLabel">Register Valid ID Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="valididtypeForm">
                    <div class="mb-3">
                        <!-- use unique IDs to avoid collisions with visitor type modal -->
                        <input type="hidden" id="valididtype_record_id" name="record_id">
                        <label for="valididtypeName" class="form-label">Valid ID Type</label>
                        <input type="text" class="form-control" id="valididtypeName" name="name"
                            placeholder="Enter Valid ID Type" autocomplete="off" required aria-describedby="valididtypeNameFeedback">
                        <div class="invalid-feedback" id="valididtypeNameFeedback">Valid ID Type is required</div>
                    </div>
                </form>
            </div>

            <!-- button -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="valididtypeSubmit">Save</button>
            </div>

        </div>
    </div>
</div>


