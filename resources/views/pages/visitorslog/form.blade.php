@extends('layout')

@section('hideSidebar')
@endsection

@section('content')

    <div class="container-background position-fixed top-0 start-0 vw-100 vh-100 bg-black"><img src="/images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div>
    <div class="addvisitor" id="addVisitorModal">
        <div class="addvisitormodal">
            <div class="panel">
            {{-- header --}}
                <a class="btn-close" href="{{ route('visitorslog') }}" id="detailsBtn">
                </a>
                <div class="header fs-3">Add Visitor</div>
                <div class="subheader mb-2">Register and record a new visitor entry</div>
                <form id="addVisitorForm" enctype="multipart/form-data">
                    @csrf 
                    <div class="form">
                        <div class="details">
                            {{-- ID Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " required>
                                <label for="id_number">ID Number</label>
                            </div>
                            {{-- Visitor Type --}}
                            <div class="input-holder floating">
                                <select name="visitor_type" id="visitor_type" class="form-control" required>
                                    <option value="" disabled selected>Select Visitor Type</option> 
                                    @foreach ($visitorTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="visitor_type">Visitor Type</label>
                            </div>
                            {{-- Visitor Type --}}
                            </select><br>
                            <div class="input-holder floating">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required>
                                <label for="first_name">first name</label>
                            </div>
                            {{-- Middle Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" " >
                                <label for="middle_name">middle name</label>
                            </div>
                            {{-- Last Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " required>
                                <label for="last_name">last name</label>
                            </div>
                            {{-- Contact Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder=" " required> 
                                <label for="contact_number">contact number</label>
                            </div>
                            {{-- Address --}}
                            <div class="input-holder floating w-100">
                                <textarea name="address" id="address" class="form-control" placeholder=" " rows="3" required></textarea>
                                <label for="address">Address</label>
                            </div>
                        </div>
                        {{-- For capturing image --}}
                        <div class="capture">
                            <div class="header">Capture Image</div>
                            <div class="imgholder" style="overflow: hidden">
                                <video id="webcam" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <img id="photoPreview" src="" style="display:none; width: 100%; height: auto;">
                            </div>

                            <button type="button" class="capture-button" id="captureBtn">Capture</button>
                            <button type="button" class="capture-button" id="recaptureBtn">ReCapture</button>
                            
                            <input type="hidden" id="image_path" name="image_path">
                        </div>

                    </div>
                    {{-- buttons --}}
                    <div class="panel-buttons">
                        <button type="submit" class="save" id="saveBtn">save</button>
                        <button type="button" class="clear" id="clrBtn">clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@vite('resources/js/visitors.js')
@endsection