

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

<form action="/home" method="get">
    <button type="submit">Home</button>
</form>

<form action="/visitors" method="get">
    <button type="submit">Visitor List</button>
</form>

<div id="result" style="margin-top:20px;"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    $('#addVisitorForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('visitor.save') }}", // ✅ correct POST route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#result')
                    .css('color', 'green')
                    .text(response.message);

                $('#addVisitorForm')[0].reset();
            },
            error: function (xhr) {
                let msg = 'Something went wrong.';
                if (xhr.status === 422) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                $('#result')
                    .css('color', 'red')
                    .text(msg);
            }
        });
    });

});
</script>

</body>
</html>
