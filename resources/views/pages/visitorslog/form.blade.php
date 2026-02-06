@extends('layout')

@section('hideSidebar')
@endsection

@section('content')

    <div class="container-background position-fixed top-0 start-0 vw-100 vh-100 bg-black"><img src="/images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div>
    <div class="addvisitor" id="addVisitorModal">
        <div class="addvisitormodal">
            <div class="panel">
                <a class="btn-close" href="{{ route('visitorslog') }}" id="detailsBtn">
                    {{-- <i class="bi bi-x-lg"></i> --}}
                </a>
                <div class="header fs-4">Add Visitor</div>
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <form id="addVisitorForm" enctype="multipart/form-data">
                    @csrf 
                    <div class="form">
                        <div class="details">
                            <div class="input-holder floating">
                                <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " required>
                                <label for="id_number">ID Number</label>
                            </div>
                            <div class="input-holder floating">
                                <select name="visitor_type" id="visitor_type" class="form-control" required>
                                    <option value="" disabled selected>Select Visitor Type</option> 
                                    @foreach ($visitorTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="visitor_type">Visitor Type</label>
                            </div>
                            </select><br>
                            <div class="input-holder floating">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required>
                                <label for="first_name">first name</label>
                            </div>
                            <div class="input-holder floating">
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" " >
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
                        <!-- <div class="capture">
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
                        </div> -->

                        <div class="capture">
                            <div class="header">Capture Image</div>
                            <div class="imgholder">
                                <video id="webcam" autoplay playsinline style="width: 100%; height: auto;"></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <img id="photoPreview" src="" style="display:none; width: 100%; height: auto;">
                            </div>

                            <button type="button" class="capture-button" id="captureBtn">Capture</button>
                            <button type="button" class="capture-button" id="recaptureBtn">ReCapture</button>
                            
                            <input type="file" id="image_path" name="image_path" hidden>
                        </div>

                    </div>
                    <div class="panel-buttons">
                        <button type="submit" class="save" id="saveBtn">save</button>
                        <button type="button" class="clear" id="clrBtn">clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
@vite('resources/js/visitors.js')
@endsection