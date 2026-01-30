@extends('layout')

@section('hideSidebar')
@endsection

@section('content')

    <div class="container-background position-fixed top-0 start-0 vw-100 vh-100 bg-black"><img src="images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div>
    <div class="addvisitor" id="addVisitorModal">
        <div class="addvisitormodal">
            <div class="panel">
                <a class="btn-close" href="{{ route('visitorlog.index') }}" id="detailsBtn">
                    {{-- <i class="bi bi-x-lg"></i> --}}
                </a>
                <div class="header fs-4">Add Visitor</div>
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <form class="form" id="addVisitorForm" >
                    {{-- @csrf enctype="multipart/form-data"--}}
                    <div class="details">
                        <div class="input-holder floating">
                            <input type="hidden" name="id" id="id" class="form-control" placeholder="">
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" ">
                            <label for="id_number">ID Number</label>
                        </div>
                        <div class="input-holder floating">
                            <select name="visitor_type" id="visitor_type" class="form-control" required>
                                <option value="" disabled selected>Select Visitor Type</option> <!-- Empty option for floating effect -->
                                @foreach ($visitorTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <label for="visitor_type">Visitor Type</label>
                        </div>
                        </select><br>
                        <div class="input-holder floating">
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" ">
                            <label for="first_name">first name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" ">
                            <label for="middle_name">middle name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" ">
                            <label for="last_name">last name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder=" ">
                            <label for="contact_number">contact number</label>
                        </div>
                        <div class="input-holder floating w-100">
                            <textarea name="address" id="address" class="form-control" placeholder=" " rows="3"></textarea>
                            <label for="address">Address</label>
                        </div>
                    </div>
                    <div class="capture">
                        <div class="header">Capture Image</div>
                        <div class="imgholder">
                            Image
                        </div>
                        {{-- <button type="button" class="capture-button">capture</button>
                        <input type="file" name="image_path" accept="image/*"> --}}
                            <button type="button" class="capture-button" id="captureBtn">
                                Capture
                            </button>
                            <input 
                                type="file"
                                id="imageInput"
                                name="image_path"
                                accept="image/*"
                                capture="user"
                                hidden
                            >
                    </div>
                    <!-- <div class="capture">
    <div class="header">Capture Image</div>
    <div class="imgholder">
        <video id="webcam" autoplay playsinline style="width: 100%; height: auto;"></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <img id="photoPreview" src="" style="display:none; width: 100%;">
    </div> 

    <button type="button" class="capture-button" id="captureBtn">Capture</button>
    
    <input type="hidden" id="image_data" name="image_path">
</div>-->
                </form>
                    <div class="panel-buttons">
                        <button type="submit" class="save">save</button>
                        <button type="button" class="clear">clear</button>
                    </div>
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
@vite('resources/js/visitors.js')



@endsection
{{-- <script>
    $(document).ready(function () {

        $('#addVisitorForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('visitor.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // ✅ success alert
                    alert(response.message);

                    // ✅ redirect after OK
                    window.location.href = "{{ route('visitorlog.index') }}";
                },
                error: function (xhr) {
                    let msg = 'Something went wrong.';

                    // ✅ Laravel validation errors (422)
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        // get FIRST validation message
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }

                    // ✅ Custom server error (500)
                    else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    alert(msg);
                }
            });
        });

    });
</script> --}}

{{-- <script>
    $(document).ready(function () {

        $('#addVisitorForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('visitor.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);

                    // ✅ redirect to visitor table
                    window.location.href = "{{ route('visitorlog.index') }}";
                },
                error: function (xhr) {
                    let msg = 'Something went wrong.';
                    if (xhr.status === 422) {
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    alert(msg);
                }
            });
        });

    });
</script> --}}