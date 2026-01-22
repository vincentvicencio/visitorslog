<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
   

    <form action="{{ route('visitor.store') }}" method="post" enctype="multipart/form-data">
        @csrf

    Add Visitor Log Here<br><br>

    <label>First Name:</label>
    <input type="text" name="first_name"><br>

    <label>Middle Name:</label>
    <input type="text" name="middle_name"><br>

    <label>Last Name:</label>
    <input type="text" name="last_name"><br>

    <label>Phone Number:</label>
    <input type="text" inputmode="numeric" pattern="[0-9]*" name="phone_number"><br>

    <label>Visitor Type:</label>
    <select name="visitor_type">
        <option disabled selected>Select Visitor Type</option>
        <option value="Applicant">Applicant</option>
        <option value="OJT">OJT</option>
        <option value="Trainee">Trainee</option>
    </select><br>

    <label>Visitor ID:</label>
    <input type="text" inputmode="numeric" pattern="[0-9]*" name="visitor_id"><br>

    <label>Location:</label>
    <input type="text" inputmode="numeric" pattern="[0-9]*" name="location"><br>

    <label>Image:</label>
    <input type="file" name="image_path" accept="image/*" capture="environment"> <br>

    <button type="submit" onclick="this.disabled=true; this.form.submit();">
    Add Visitor
</button>

</form>
<form action="/home" method="get">
    <button type="submit">Home</button>
</form>

</body>
</html>