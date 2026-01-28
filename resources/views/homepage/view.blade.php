@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visitor Details</title>
</head>
<body>
    <h3>Visitor Details</h3>

    <label>First Name:</label>
    <input type="text" name="first_name" value="{{ $visitor->first_name }}" required><br>

    <label>Middle Name:</label>
    <input type="text" name="middle_name" value="{{ $visitor->middle_name }}"><br>

    <label>Last Name:</label>
    <input type="text" name="last_name" value="{{ $visitor->last_name }}" required><br>

    <label>Phone Number:</label>
    <input type="text" name="phone_number" value="{{ $visitor->phone_number }}"><br>

    <label>Visitor Type:</label>
    <input type="text" name="visitor_type" value="{{ $visitor->visitor_type }}" required><br>

    <label>Visitor ID:</label>
    <input type="text" name="visitor_id" value="{{ $visitor->visitor_id }}" required><br>

    <label>Location:</label>
    <input type="text" name="location" 
    value="
        @if ($visitor->location == 1)
            Facility Center
        @elseif ($visitor->location == 2)
            Summit One
        @else
            Mezzanine
        @endif
        "><br>

    <label>Image:</label>
    <img src="{{ Storage::url($visitor->image_path) }}" alt="Image" ><br><br>

    <a class="dropdown-item" href="{{ route('visitorlog.index') }}" id="detailsBtn">
        <i class="bi bi-eye me-2"></i> Back
    </a>

</body>
</html>
@endsection
