<div class="modal fade" id="textInputModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="textInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="textInputModalLabel">Register Visitor Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form id="textInputForm">
                    <div class="mb-3">
                        <input type="hidden" id="record_id" name="record_id">
                        <label for="name" class="form-label">Visitor Type</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Enter Visitor Type" autocomplete="off" required aria-describedby="visitorTypeNameFeedback">
                        <div class="invalid-feedback" id="visitorTypeNameFeedback">Visitor Type is required</div>
                    </div>
                </form>
            </div>

            <!-- button -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="textInputSubmit">Submit</button>
            </div>

        </div>
    </div>
</div>


