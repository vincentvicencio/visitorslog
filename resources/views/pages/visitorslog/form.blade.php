@extends('layout')

@section('hideSidebar')
@endsection

@section('content')
    <div class="loginbackground1 container-background position-fixed top-0 start-0 vw-100 vh-100">
        <img src="/images/login-background-1.png" alt="" class="background-image-1 w-100 h-100 object-fit-cover">
        <img src="/images/login-background-2.png" alt="" class="background-image-2 object-fit-cover">
        <div class="shadow"></div>
        <div class="background-image-4"><img src="/images/Magellan-Logo-w-Tagline.png" alt="" class=""></div>
    </div>
    <div class="addvisitor" id="addVisitorModal">
        <div class="addvisitormodal">
            <div class="panel">
            {{-- header --}}
            {{-- @if(Auth::user()->user_type == 1)
                <a class="btn-close" href="{{ route('visitorslog') }}" id="detailsBtn">
                </a>
            @endif --}}
                <div class="header fs-3">Log Visitor</div>
                <div class="subheader mb-2">Register and record a new visitor entry</div>
                <form id="addVisitorForm" enctype="multipart/form-data">
                    @csrf 
                    <div class="form">
                        <div class="details">

                            <div class="personaldetails w-100">
                                Visitor Information
                            </div>
                            
                            {{-- Visitor Type --}}
                            <div class="input-holder floating">
                                <select name="visitor_type" id="visitor_type" class="form-control" required autocomplete="off">
                                    <option value="" disabled selected>Select Visitor Type</option> 
                                    @foreach ($visitorTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                    
                                </select>
                                <label for="visitor_type">Visitor Type</label>
                            </div>
                            {{-- Visitor ID Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="id_number">Visitor ID Number</label>
                                <div id="id_suggestions" class="list-group position-absolute w-100" style="z-index:999; display:none;"></div>
                            </div>

                            {{-- Contact person --}}
                            <br>
                            <div class="input-holder floating">
                                <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="contact_person">Contact Person</label>
                            </div>
                            {{-- Purpose of visit --}}
                            <div class="input-holder floating">
                                <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="purpose_of_visit">Purpose of Visit</label>
                            </div>
                            {{-- Affiliation --}}
                            <div class="input-holder floating">
                                <input type="text" name="affiliation" id="affiliation" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="affiliation">Affiliation</label>
                            </div>

                            <br>
                            <div class="personaldetails w-100 mt-3">
                                personal details
                            </div>


                            {{-- First Name --}}
                            <br>
                            <div class="input-holder floating">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="first_name">first name</label>
                            </div>
                            {{-- Middle Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder=" " autocomplete="off">
                                <label for="middle_name">middle name</label>
                            </div>
                            {{-- Last Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="last_name">last name</label>
                            </div>
                            {{-- Contact Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder=" " required autocomplete="off"> 
                                <label for="contact_number">contact number</label>
                            </div>


                            {{-- identification card --}}
                            <div class="input-holder floating">
                                <select name="id_type" id="id_type" class="form-control" required autocomplete="off">
                                    <option value="" disabled selected>Identification Type</option> 
                                    @foreach ($validIdTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->id_type_name }}</option>
                                    @endforeach
                                </select>
                                <label for="id_type">Identification Card</label>
                            </div>
                            {{-- ID Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="id_type_number" id="id_type_number" class="form-control" placeholder=" " required autocomplete="off">
                                <label for="id_type_number">ID Number</label>
                            </div>


                            {{-- Address --}}
                            <div class="input-holder floating w-100">
                                <textarea name="address" id="address" class="form-control" placeholder=" " rows="3" required autocomplete="off"></textarea>
                                <label for="address">Address</label>
                            </div>
                            {{-- buttons --}}
                            <div class="panel-buttons" id="desktop">
                                <button type="submit" class="save" id="saveBtn">save</button>
                                <button type="button" class="clear" id="clrBtn">clear</button>
                            </div>


                        </div>
                        {{-- For capturing image --}}

                        <div class="capture">
                            <div class="header">Capture Image</div>
                                <div class="imgholder" style="overflow: hidden">
                                    <video id="webcam" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                    <canvas id="canvas" style="display:none;"></canvas>
                                    <input type="file" name="imageInput" id="imageInput" accepts="image/*" capture="user" hidden>
                                    <img id="photoPreview" src="" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                                    <input type="hidden" id="image_path" name="image_path">
                                    <input type='hidden' id="webcamUse" value="0">
                                </div>

                            <button type="button" class="capture-button" id="captureBtn">Capture</button>
                            <button type="button" class="capture-button" id="recaptureBtn">ReCapture</button>
                        
                        </div>
                        {{-- buttons --}}
                        <div class="panel-buttons" id="mobile">
                            <button type="submit" class="save" id="saveBtn">save</button>
                            <button type="button" class="clear" id="clrBtn">clear</button>
                        </div>

                    </div>
                    
                </form>
            </div>
        </div>
    </div>

@vite('resources/js/visitors.js')
@endsection