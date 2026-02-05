<div class="modal fade notification-wrapper" id="notificationContainer" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="notificationContainer-label" role="dialog"
    aria-hidden="true" style="display:none">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notification-title"></h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>

            <div class="modal-body">
                <div class="notification">
                    <div class="card border-0 shadow-none">
                        <div class="row g-0 d-flex justify-content-center">
                            <div class="card-body">
                                <div class="notification-body">
                                    <div class="row">
                                        <div class="col-md-12 py-3">
                                            <h5 class="card-title text-center" id="notification-message"></h5>
                                            <form id="timeOutForm">
                                                <input type="hidden" name="record_id" id="record_id" class="notification_record">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-secondary btn-cancel col-3 me-2"
                                            data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                        <button type="button" class="btn btn-danger col-3" id="btn_ok">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- {{-- @vite(['resources/js/registered.js', 'resources/js/visitors.js', 'resources/js/visitortype.js']) --}} -->