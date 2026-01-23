<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Visitor IDs</title>
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
            text-align: left;
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
    </style>
</head>
<body>

<h2>Registered Visitor IDs</h2>

<a href="/home" class="home-button">Home</a>
<form action="/IDNumber" method="post">
    @csrf
    <button type="submit" class="home-button">Register ID</button>
</form>

<table>
    <thead>
        <tr>
            <th>Visitor Type</th>
            <th>Visitor ID</th>
            <th>Created By</th>
            <th>updated By</th>
            <th>Created Date</th>
            <th>Updated Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($registeredIds as $index => $visitor)
        <tr>
            @foreach ($visitorTypes as $type)
                @if ($type->id == $visitor->visitor_type)
                    <td>{{ $type->name }}</td>
                @endif
            @endforeach
            {{-- <td>{{ $visitor->visitor_type }}</td> --}}
            <td>{{ $visitor->id_number }}</td>
            <td>{{ $visitor->created_by }}</td>
            <td>{{ $visitor->updated_by }}</td>
            <td>{{ $visitor->created_at->format('Y-m-d H:i') }}</td>    
            <td>{{ $visitor->updated_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;">No visitor IDs registered yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
