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
                <div class="header fs-3">Log Employee</div>
                <div class="subheader mb-2">Create a new record of entry</div>
                <form id="addVisitorForm" enctype="multipart/form-data">
                    @csrf 
                    <div class="form">
                        <div class="details">

                            <div class="personaldetails w-100">
                                Employee Details
                            </div>

                        
                            <div class="search-bar input-holder floating w-100">
                                <input type="text"id="typeSearch" placeholder="Enter Emp code or Name" class="search" >
                                <button class="m-0"><i class="bi bi-search"></i></button>
                            </div>
                            <div class="input-holder floating">
                                <input type="text" name="first_name" id="first_name" class="form-control"  placeholder=" " value="">
                                <label for="first_name">First name</label>
                            </div>
                            {{-- Middle Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="middle_name" id="middle_name" class="form-control"  placeholder=" " value="">
                                <label for="middle_name">Middle name</label>
                            </div>
                            {{-- Last Name --}}
                            <div class="input-holder floating">
                                <input type="text" name="last_name" id="last_name" class="form-control"  placeholder=" " value="">
                                <label for="last_name">Last name</label>
                            </div>
                            <div class="input-holder floating">
                                <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="" >
                                <label for="purpose_of_visit">Employee Code</label>
                            </div>
                            <div class="input-holder floating">
                                <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="" >
                                <label for="purpose_of_visit">Location</label>
                            </div>
                            <div class="input-holder floating">
                                <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="Currently Logged out" >
                                <label for="purpose_of_visit">Status</label>
                            </div>

                            <br><br><br><br>
                            <br>
                            {{-- buttons --}}
                            <div class="panel-buttons" id="desktop">
                                <button type="submit" class="save" id="saveBtn">Time in</button>
                                <button type="button" class="clear" id="clrBtn">clear</button>
                            </div>


                        </div>

                        <div class="capture">
                            <div class="header">Image</div>
                                <div class="imgholder" style="overflow: hidden">
                                    <video id="webcam" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                    <canvas id="canvas" style="display:none;"></canvas>
                                    <input type="file" name="imageInput" id="imageInput" accepts="image/*" capture="user" hidden>
                                    <img id="photoPreview" src="" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                                    <input type="hidden" id="image_path" name="image_path">
                                    <input type='hidden' id="webcamUse" value="0">
                                </div>
                        </div>
                        {{-- buttons --}}
                        <div class="panel-buttons" id="mobile">
                            <button type="submit" class="save" id="saveBtn">Time in</button>
                            <button type="button" class="clear" id="clrBtn">clear</button>
                        </div>

                    </div>
                    
                </form>
            </div>
        </div>
    </div>

@vite('resources/js/visitors.js')
@endsection