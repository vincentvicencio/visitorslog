
@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Visitor</title>
</head>
<body>

<form id="addVisitorForm" enctype="multipart/form-data">
    @csrf

    <h3>Add Visitor Log</h3>

    <label>First Name:</label>
    <input type="text" name="first_name" required><br>

    <label>Middle Name:</label>
    <input type="text" name="middle_name"><br>

    <label>Last Name:</label>
    <input type="text" name="last_name" required><br>

    <label>Phone Number:</label>
    <input type="text" name="phone_number"><br>

    <label>Visitor Type:</label>
    <select name="visitor_type" required>
        <option value="" disabled selected>Select Visitor Type</option>
        @foreach ($visitors as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
        @endforeach
    </select><br>

    <label>Visitor ID:</label>
    <input type="text" name="visitor_id" required><br>

    <label>Location:</label>
    <input type="text" name="location"><br>

    <label>Image:</label>
    <input type="file" name="image_path" accept="image/*"><br><br>

    <button type="submit">Add Visitor</button>
</form>

<br>

{{-- <form action="/visitorlog" method="post">
    @csrf
    <button type="submit">Visitor List</button>
</form> --}}
 <a class="dropdown-item" href="{{ route('visitorlog.index') }}" id="detailsBtn">
    <i class="bi bi-eye me-2"></i> Back
</a>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        $('#addVisitorForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('visitor.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // ✅ success alert
                    alert(response.message);

                    // ✅ redirect after OK
                    window.location.href = "{{ route('visitorlog.index') }}";
                },
                error: function (xhr) {
                    let msg = 'Something went wrong.';

                    // ✅ Laravel validation errors (422)
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        // get FIRST validation message
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }

                    // ✅ Custom server error (500)
                    else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    alert(msg);
                }
            });
        });

    });
</script>

{{-- <script>
    $(document).ready(function () {

        $('#addVisitorForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('visitor.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);

                    // ✅ redirect to visitor table
                    window.location.href = "{{ route('visitorlog.index') }}";
                },
                error: function (xhr) {
                    let msg = 'Something went wrong.';
                    if (xhr.status === 422) {
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    alert(msg);
                }
            });
        });

    });
</script> --}}


</body>
</html>
@endsection