@extends('layouts.app')

@section('content')

<div class="container p-0 m-0 vh-100">
    <div class="container-background position-fixed top-0 start-0 vw-100 vh-100 bg-black"><img src="images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div>
    <div class="center-card">
        <div class="main-card d-flex flex-row bg-white p-1 gap-2 m-3 rounded">
            <div class="main-card-panel position-relative overflow-hidden" id="panel-01">
                <img src="images/compass.png" alt="" class="background-logo position-absolute h-100 start-100">
                <img src="images/logo.png" alt="" class="position-absolute bottom-0 start-0"style="height: 100px;">
            </div>
            <div class="main-card-panel" id="panel-02">
                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column justify-content-center h-100 p-3">
                    <div class="form-header mb-3">
                        <div class="form-mainheader fw-bold fs-3">Sign in</div>
                        <div class="form-subheader">enter your login credentials</div>
                        <img src="images/compass.png" alt="" class="position-absolute top-0 end-0 d-none" style="height: 60px;">
                    </div>                    
                    @csrf
                    <!-- EMAIL ADDRESS -->
                    <div class="input-holder">
                        <label for="email" class="">{{ __('Email Address') }}</label>

                        <div class="">
                            <input id="emp_code" type="text" class="form-control" name="emp_code" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- PASSWORD -->
                    <div class="input-holder">
                        <label for="password" class="">{{ __('Password') }}</label>

                        <div class="">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- FORGOT AND LOGIN BUTTON -->
                    <div class="login-button d-flex align-items-center justify-content-end mt-4 mb-1">
                        <button type="submit" class="btn text-white w-300px border-0">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
