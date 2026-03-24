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
                <a class="btn-close" href="{{ route('visitorslog') }}" id="detailsBtn">
                </a>
                <div class="header fs-3">Log Employee</div>
                <div class="subheader mb-2">Create a new record of entry</div>
                <form id="logemp_form" enctype="multipart/form-data">
                    @csrf 
                    <div class="form">
                        <div class="details">

                            <div class="personaldetails w-100">
                                Employee Details
                            </div>

                            <div class="search-bar input-holder floating w-100">
                                <input type="text" id="logemp_emp_code" placeholder="Enter Emp Code or Name" class="search" autocomplete="off">
                                <button class="m-0" type="button" id="search_emp_btn"><i class="bi bi-search"></i></button>
                            </div>
                             <div class="input-holder floating">
                                <input type="text" id="searched_emp_code" class="form-control" placeholder=" " readonly>
                                <label>Employee Code</label>
                            </div>
                            <div class= "input-holder floating">
                                <input type="text" name="full_name" id="logemp_full_name" class="form-control" placeholder=" " readonly>
                                <label>Full Name</label>
                            </div>
                            <div class="input-holder floating">
                                <select name="status" id="status" class="form-control" required autocomplete="off">
                                    <option value="" disabled selected>Select Status</option> 
                                    <option value=0>In</option> 
                                    <option value=1>Out</option> 
                                </select>
                                <label for="status">Status</label>
                            </div>
                            <div class= "input-holder floating">
                                <input type="text" name="activity" id="activity" class="form-control" placeholder=" " required autocomplete="off">
                                <label>Activity</label>
                            </div>
                            {{-- buttons --}}
                            <div class="panel-buttons" id="desktop">
                                <button type="submit" class="save" id="saveBtn">Log</button>
                                <button type="button" class="clear" id="clrBtn">clear</button>
                            </div>

                        </div>

                        <div class="capture">
                            <div class="header">Image</div>
                                <div class="imgholder" style="overflow: hidden">
                                    <img id="photoPreview" src="" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                                    <input type="hidden" id="image_path" name="image_path">
                                    <input type='hidden' id="webcamUse" value="0">
                                </div>
                        </div>
                        {{-- buttons --}}
                        <div class="panel-buttons" id="mobile">
                            <button type="submit" class="save" id="saveBtn">Log</button>
                            <button type="button" class="clear" id="clrBtn">clear</button>
                        </div>

                    </div>
                    
                </form>
            </div>
        </div>
    </div>

@vite('resources/js/employees.js')
@endsection