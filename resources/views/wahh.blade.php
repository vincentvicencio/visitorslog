<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <button type="button" id="openPopup" class="btn btn-primary">Add User Type</button>
    </div>

    <div class="container mt-5">
        <button type="button" id="openPopup2" class="btn btn-primary">Register User</button>
    </div>
    @include('popup') 
    
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Open/Close Popup Logic
    $('#openPopup').click(function() { $('#popupContainer').fadeIn(); });
    $('#closePopup').click(function() { $('#popupContainer').fadeOut(); });

    // Handle AJAX Submission
    $('#user_type_form').on('submit', function(e) {
        e.preventDefault(); // This PREVENTS the GET error by stopping the page reload

        $.ajax({
            url: "{{ route('addusertype') }}", // Blade helper works here
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}", // Blade helper works here
                user_type: $('#user_type').val()
            },
            success: function(response) {
                alert("Saved successfully!");
                location.reload();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Something went wrong. Check the console.");
            }
        });
    });

    $('#openPopup2').click(function() { $('#popupContainer2').fadeIn(); });
$('#closePopup2').click(function() { $('#popupContainer2').fadeOut(); });

// Handle AJAX Submission
$('#registered_user_form').on('submit', function(e) {
    e.preventDefault(); 

    $.ajax({
        url: "{{ route('addusers') }}", 
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            // Update these to match the new IDs we just created
            emp_code: $('#reg_emp_code').val(),
            password: $('#reg_password').val(),
            user_type: $('#reg_user_type').val() 
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            // This will now show you the specific validation error from Laravel
            console.log(xhr.responseText);
            alert("Validation Error: " + xhr.responseJSON.message);
        }
    });
});
});
</script>


//controller
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usertype;
use Illuminate\Support\Facades\Auth; // Required to get the user ID
use App\Models\User_types;

class User_TypesController extends Controller
{
public function addusertype(Request $request)
{
    $userTypeName = $request->input('user_type');

    \App\Models\User_types::create([
        'name'       => $userTypeName,
        'created_by' => Auth::id() ?? 1,
        'updated_by' => Auth::id() ?? 1,
        'deleted_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ]);

    return response()->json(['status' => 'success']);
}
}


//model
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_types extends Model
{
    protected $table = 'user_types';

    // Add updated_by to this list
    protected $fillable = ['name', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at']; 
}

//route
Route::post('/addusertype', [User_TypesController::class, 'addusertype'])->name('addusertype');    