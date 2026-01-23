@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <form action="/visitor" method="post">
                        @csrf
                        <button type="submit">Add Visitor</button>
                    </form>

                    <form action="/IDNumber" method="post">
                        @csrf
                        <button type="submit">Register ID</button>
                    </form>

                    <form action="/visitor_type" method="post">
                        @csrf
                        <button type="submit">Add Visitor Type</button>
                    </form>

                    {{-- <form action="/visitor_type" method="post">
                        @csrf
                        <button type="submit">Try List</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
