@extends('layout')

@section('content')
<div class="currentlocation">
    <div class="panel location-panel">
        <div class="header location-header">
            <div class="maintitle location-title">enter current location</div>
            <div class="subtitle location-subtitle">Please specify where you are currently located</div>
        </div>
        <form method="POST" action="{{ route('guard.location.store') }}" class="location-form">
            @csrf
            <div class="mb-3 location-field">
                <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                    <option value="">Select location</option>
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                        </option>
                        @endforeach
                        </select>
                        @error('location_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 location-submit">Continue</button>
        </form>
    </div>
</div>
@endsection
