@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Visitor</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>

<form id="visitorTypeForm"">
    @csrf

    <h3>Adding Visitor Type</h3>

    <label>Visitor Type:</label>
    <input type="text" name="visitor_type" autocomplete="off"><br><br>

    <button type="submit">Add Visitor Type</button>
</form>

<form action="/home" method="get">
    <button type="submit">Home</button>
</form>
{{-- <form action="/visitortype" method="post">
    @csrf
    <button type="submit">Visitor Type List</button>
</form> --}}

 <a class="dropdown-item" href="{{ route('visitortype.index') }}" id="detailsBtn">
        <i class="bi bi-eye me-2"></i> Back
    </a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        $('#visitorTypeForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('visitorType.save') }}",
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
                    window.location.href = "{{ route('visitortype.index') }}";
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


</body>
</html>
@endsection