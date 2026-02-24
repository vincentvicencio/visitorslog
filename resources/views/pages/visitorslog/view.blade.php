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
                        {{-- ID Number --}}
                        <div class="input-holder floating">
                            <input type="hidden" name="id" id="id" class="form-control" placeholder="" value="{{ $visitor->id }}">
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " readonly value="{{ $visitor->visitor_id }}">
                            <label for="id_number">ID Number</label>
                        </div>
                        {{-- Visitor Type --}}
                        <div class="input-holder floating">
                            @foreach ($visitorTypes as $type)
                                @if ($visitor->visitor_type == $type->id)
                                    <input type="text" name="visitor_type" class="form-control" readonly value="{{ $type->name }} " required>
                                @endif
                            @endforeach
                            <label for="visitor_type">Visitor Type</label>
                        </div>
                        {{-- Visitor Type --}}
                        </select><br>
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
                                No Image Provided
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