@extends('layout')

@section('hideSidebar')
@endsection

@section('content')

    <div class="container-background position-fixed top-0 start-0 vw-100 vh-100" style="background-color: rgb(0,0,0,0.5)"><img src="/images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div>
    <div class="addvisitor" id="addVisitorModal">
        <div class="addvisitormodal">
            <div class="panel">
                <a class="btn-close" href="{{ route('visitorlog.index') }}" id="detailsBtn">
                    <!-- {{-- <i class="bi bi-x-lg"></i> --}} -->
                </a>
                <div class="header fs-4">Visitor Details</div>
                <div class="subheader mb-3">Register and record a new visitor entry</div>
                <div class="form" id="addVisitorForm">
                    @csrf
                    <div class="details">
                        <div class="input-holder floating">
                            <input type="hidden" name="id" id="id" class="form-control" placeholder="" value="{{ $visitor->id }}">
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder=" " readonly value="{{ $visitor->visitor_id }}">
                            <label for="id_number">ID Number</label>
                        </div>
                        <div class="input-holder floating">
                            @foreach ($visitorTypes as $type)
                                @if ($visitor->visitor_type == $type->id)
                                    <input type="text" name="visitor_type" class="form-control" readonly value="{{ $type->name }} " required>
                                @endif
                            @endforeach
                            <label for="visitor_type">Visitor Type</label>
                        </div>
                        </select><br>
                        <div class="input-holder floating">
                            <input type="text" name="first_name" id="first_name" class="form-control" readonly placeholder=" " value="{{ $visitor->first_name }}">
                            <label for="first_name">First name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="middle_name" id="middle_name" class="form-control" readonly placeholder=" " value="{{ $visitor->middle_name }}">
                            <label for="middle_name">Middle name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="last_name" id="last_name" class="form-control" readonly placeholder=" " value="{{ $visitor->last_name }}">
                            <label for="last_name">Last name</label>
                        </div>
                        <div class="input-holder floating">
                            <input type="text" name="contact_number" id="contact_number" class="form-control" readonly placeholder=" " value="{{ $visitor->phone_number }}">
                            <label for="contact_number">Contact Number</label>
                        </div>
                        <div class="input-holder floating w-100">
                                @if ($visitor->location == 1)
                                    <textarea name="address" id="address" class="form-control" readonly placeholder=" " rows="3">Facility Center</textarea>
                                @elseif ($visitor->location == 2)
                                    <textarea name="address" id="address" class="form-control" readonly placeholder=" " rows="3">Summit One</textarea>
                                @else
                                    <textarea name="address" id="address" class="form-control" readonly placeholder=" " rows="3">Mezzanine</textarea>
                                @endif
                            
                            <label for="address">Address</label>
                        </div>
                    </div>
                    <div class="capture">
                        <div class="header">Capture Image</div>
                        <div class="imgholder">
                            @if ( $visitor->image_path == null)
                                No Image Provided
                            @else
                                <img src="{{ Storage::url($visitor->image_path) }}" alt="" class="w-100 h-100 object-fit-cover">
                            @endif 
                        </div>
                        {{-- <button type="button" class="capture-button">capture</button>
                        <input type="file" name="image_path" accept="image/*"> --}}
                        {{-- @if ($visitor->image_path == null)
                            <button type="button" class="capture-button" id="captureBtn">
                                Capture
                            </button>
                            <input 
                                type="file"
                                id="imageInput"
                                name="image_path"
                                accept="image/*"
                                capture="environment"
                                hidden
                            >
                        @endif  --}}
                    </div>
                </div>
                {{-- @if ($visitor->image_path == null)
                    <div class="panel-buttons">
                        <button type="submit" class="save">save</button>
                        <button type="button" class="clear">clear</button>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
@vite('resources/js/visitors.js')



@endsection

