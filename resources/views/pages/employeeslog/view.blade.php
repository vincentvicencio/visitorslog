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
                <div class="header fs-4">Employeee Details</div>
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <div class="form" id="addVisitorForm">
                    @csrf
                    <div class="details">
                        <div class="personaldetails w-100" style="height:35px">
                            Employee Details
                        </div>

                        
                        <!-- <div class="input-holder floating w-100">
                            <input type="text" name="id_type_number" id="id_type_number" class="form-control" placeholder=" " value="" readonly>
                            <label for="id_type_number">Search</label>
                        </div> -->

                        <br>
                        <div class="input-holder floating">
                            <input type="text" name="first_name" id="first_name" class="form-control" readonly placeholder=" " value="">
                            <label for="first_name">First name</label>
                        </div>
                        {{-- Middle Name --}}
                        <div class="input-holder floating">
                            <input type="text" name="middle_name" id="middle_name" class="form-control" readonly placeholder=" " value="">
                            <label for="middle_name">Middle name</label>
                        </div>
                        {{-- Last Name --}}
                        <div class="input-holder floating">
                            <input type="text" name="last_name" id="last_name" class="form-control" readonly placeholder=" " value="">
                            <label for="last_name">Last name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="" readonly>
                            <label for="purpose_of_visit">Employee Code</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="" readonly>
                            <label for="purpose_of_visit">Location</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="Currently Logged out" readonly>
                            <label for="purpose_of_visit">Status</label>
                        </div>

                        <br><br><br><br><br><br>
                    </div>
                    {{-- for capturing image --}}
                    <div class="capture">
                        <div class="header">Image</div>
                        <div class="imgholder">
                        </div>
                    </div>
                </div>
                <br><br><br><br>
            </div>
        </div>
    </div>

@vite('resources/js/visitors.js')

@endsection