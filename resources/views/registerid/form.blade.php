

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Visitor</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>

<form id="registerIDForm">
    @csrf

    <h3>Register Visitor ID</h3>

    <label>Register ID:</label>
    <input type="text" inputmode="numeric" pattern="[0-9]*" name="visitor_id"><br><br>

    <label>Visitor Type:</label>
    {{-- <select name="visitor_type">
        <option disabled selected>Select Visitor Type</option>
        <option value="Applicant">Applicant</option>
        <option value="OJT">OJT</option>
        <option value="Trainee">Trainee</option>
    </select><br><br> --}}
    {{-- <select name="visitor_type" required>
        <option value="" disabled selected>Select Visitor Type</option>

        @forelse ($visitorTypes as $type)
            <option value="{{ $type->name }}">{{ $type->name }}</option>
        @empty
            <option disabled>No visitor types available</option>
        @endforelse
    </select> --}}
    {{-- <select name="visitor_type" required>
    <option value="" disabled selected>Select Visitor Type</option>

    @foreach ($visitorTypes as $type)
        <option value="{{ $type->name }}">
            {{ $type->name }}
        </option>
    @endforeach
</select> --}}
    <select name="visitor_type" required>
        <option value="" disabled selected>Select Visitor Type</option>
        @foreach ($visitorTypes as $type)
            <option value="{{ $type->id }}">
                {{ $type->name }}
            </option>
        @endforeach
    </select>


<br><br>


    <br><br>


    <button type="submit">Register Visitor ID</button>
</form>

<form action="/home" method="get">
    <button type="submit">Home</button>
</form>
<form action="/registeredIDs" method="get">
    @csrf
    <button type="submit">Registered ID</button>
</form>

<div id="result" style="margin-top:20px;"></div>

<script>
$(document).ready(function() {
    $('#registerIDForm').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: '/registeredID/save',
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#result').css('color', 'green').text(response.message);
                    $('#registerIDForm')[0].reset();
                } else {
                    $('#result').css('color', 'red').text(response.message || 'Error registering Visitor ID.');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if(errors) {
                    let errorMessages = Object.values(errors).flat().join(' ');
                    $('#result').css('color', 'red').text(errorMessages);
                } else {
                    $('#result').css('color', 'red').text('An unexpected error occurred.');
                }
            }
        });
    });
});

</script>

</body>
</html>
