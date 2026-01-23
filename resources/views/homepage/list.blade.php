<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Visitor</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        .home-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 8px;
            text-align: center;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
        }
        .timeout-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 8px;
            text-align: center;
            background-color: #dc3434;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
        }
    </style>
</head>
<body>

<h2>Registered Visitor</h2>

<a href="/home" class="home-button">Home</a>
<form action="/visitor" method="post">
    @csrf
    <button type="submit" class="home-button">Add Visitor</button>
</form>

<table>
    <thead>
        <tr>
            <th>Personal Details</th>
            <th>Visitor Type</th>
            <th>ID No.</th>
            <th>Image</th>
            <th>Visit Date</th>
            <th>Time</th>
            <th>By</th>
            <th>Status</th>
            <th>Action</th>

        </tr>
    </thead>
    <tbody>
        @forelse($visitors as $index => $visitor)
        <tr>
            <td>
                {{ $visitor->first_name }} {{ $visitor->middle_name }} {{ $visitor->last_name }}
                <br>{{ $visitor->location }} 
                <br>{{ $visitor->phone_number }}
            </td>
            <td>{{ $visitor->visitor_type }}</td>
            <td>{{ $visitor->visitor_id }}</td>
            <td><button class="home-button">View Image</button></td>
            {{-- <img src="{{ asset('storage/' . $visitor->image_path) }}" alt="Visitor Image" width="100"> --}}
            <td>{{ $visitor->created_at->format('Y-m-d') }}</td>
            <td>In: {{ $visitor->time_in}} Out: {{ $visitor->time_out }}</td>
            <td>{{ $visitor->created_by }}</td>
            <td>{{ $visitor->status }}</td>
            <td><button class="home-button">View Details</button> <button class="timeout-button">Timeout</button></td>
            {{-- <a href="/visitors/{{ $visitor->id }}/edit">Edit</a> --}}
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center;">No visitors registered yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
