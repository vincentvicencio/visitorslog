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
                <a class="btn-close" href="{{ route($type) }}"></a>
                <div class="header fs-4">Visitor Details</div>
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <div class="form" id="addVisitorForm">
                    @csrf
                    <div class="details">
                        <div class="personaldetails w-100">
                            Visitor Information
                        </div>
                        {{-- Visitor Type --}}
                        <div class="input-holder floating">
                            @php
                                $matchedType = $visitorTypes->firstWhere('id', $visitor->visitor_type);
                            @endphp

                            <input type="text"
                                name="visitor_type"
                                class="form-control"
                                readonly
                                value="{{ $matchedType ? $matchedType->name : '--' }}"
                                required>
                            <label for="visitor_type">Visitor Type</label>
                        </div>
                        
                        {{-- ID Number --}}
                        <div class="input-holder floating">
                            <input type="hidden" name="id" id="id" class="form-control" placeholder="" value="{{ $visitor->id }}">
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " readonly value="{{ $visitor->visitor_id }}">
                            <label for="id_number">ID Number</label>
                        </div>

                        {{-- Contact person --}}
                        <br>
                        <div class="input-holder floating">
                            <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder=" " value="{{ $visitor->contact_person }}" readonly>
                            <label for="contact_person">Contact Person</label>
                        </div>
                        {{-- Purpose of visit --}}
                        <div class="input-holder floating">
                            <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control" placeholder=" " value="{{ $visitor->purpose }}" readonly>
                            <label for="purpose_of_visit">Purpose of Visit</label>
                        </div>

                        <br>
                        <div class="personaldetails w-100 mt-3">
                            personal details
                        </div>

                        {{-- first name --}}
                        <br>
                        <div class="input-holder floating">
                            <input type="text" name="first_name" id="first_name" class="form-control" readonly placeholder=" " value="{{ $visitor->first_name }}">
                            <label for="first_name">First name</label>
                        </div>
                        {{-- Middle Name --}}
                        <div class="input-holder floating">
                            <input type="text" name="middle_name" id="middle_name" class="form-control" readonly placeholder=" " value="{{ $visitor->middle_name ? $visitor->middle_name : '--'}}">
                            <label for="middle_name">Middle name</label>
                        </div>
                        {{-- Last Name --}}
                        <div class="input-holder floating">
                            <input type="text" name="last_name" id="last_name" class="form-control" readonly placeholder=" " value="{{ $visitor->last_name }}">
                            <label for="last_name">Last name</label>
                        </div>
                        {{-- Contact Number --}}
                        <div class="input-holder floating">
                            <input type="text" name="contact_number" id="contact_number" class="form-control" readonly placeholder=" " value="{{ $visitor->phone_number }}">
                            <label for="contact_number">Contact Number</label>
                        </div>
                        {{-- ID Type --}}
                        <div class="input-holder floating">
                            @php
                                $matchedIDType = $validIdTypes->firstWhere('id', $visitor->id_type);
                            @endphp

                            <input type="text"
                                name="id_type"
                                class="form-control"
                                value="{{ $matchedIDType ? $matchedIDType->id_type_name : '--' }}"
                                readonly>
                                <label for="id_type">Identification Card</label>
                            </div>
                            {{-- ID Number --}}
                            <div class="input-holder floating">
                                <input type="text" name="id_type_number" id="id_type_number" class="form-control" placeholder=" " value="{{ $visitor->valid_id }}" readonly>
                                <label for="id_type_number">ID Number</label>
                            </div>
                        {{-- Address --}}
                        <div class="input-holder floating w-100">
                            <textarea name="address" id="address" class="form-control" readonly placeholder=" " rows="3">{{ $visitor->address }}</textarea>
                            <label for="address">Address</label>
                        </div>
                    </div>
                    {{-- for capturing image --}}
                    <div class="capture">
                        <div class="header">Capture Image</div>
                        <div class="imgholder">
                            @if ( $visitor->image_path == null)
                                <span style="color: rgba(128, 128, 128, 0.568); font-weight: bold;">No Image Provided</span> 
                            @else
                                <img src="{{ Storage::url($visitor->image_path) }}" alt="" class="w-100 h-100 object-fit-cover">
                            @endif 
                        </div>
                    </div>
                </div>
                <br><br><br><br>
            </div>
        </div>
    </div>

@vite('resources/js/visitors.js')

@endsection