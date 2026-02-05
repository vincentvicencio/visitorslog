<!-- Modal -->
<div class="modal fade" id="textInputModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="textInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="textInputModalLabel">Register Visitor Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="textInputForm">
                    <div class="mb-3">
                        <label for="userInput" class="form-label">Visitor Type</label>
                        <input type="text" class="form-control" id="userInput" name="userInput"
                            placeholder="Enter Visitor Type" autocomplete="off" required>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> --}}
                <button type="button" class="btn btn-primary" id="textInputSubmit">Submit</button>
            </div>

        </div>
    </div>
</div>


