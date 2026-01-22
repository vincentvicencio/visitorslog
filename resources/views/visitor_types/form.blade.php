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
<form action="/visitorTypes" method="get">
    @csrf
    <button type="submit">Visitor Type List</button>
</form>

<div id="result" style="margin-top:20px;"></div>

<script>
$(document).ready(function() {
    $('#visitorTypeForm').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: '/visitorType/save',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#result')
                        .css('color', 'green')
                        .text(response.message);

                    $('#visitorTypeForm')[0].reset();
                } else {
                    $('#result')
                        .css('color', 'red')
                        .text(response.message || 'Error Adding Visitor Type.');
                }
            },
            error: function(xhr) {   // ✅ fixed
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $('#result')
                        .css('color', 'red')
                        .text(errors.visitor_type[0]); // validation message
                } else {
                    $('#result')
                        .css('color', 'red')
                        .text('Unexpected error occurred.');
                }
            }
        });
    });
});
</script>


</body>
</html>
