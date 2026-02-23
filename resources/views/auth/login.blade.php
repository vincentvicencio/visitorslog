@extends('layouts.app')

@section('content')
<!-- <div class="qwerty"><img src="images/image.png" alt=""></div> -->
<div class="container p-0 m-0 vh-100">
    <div class="loginbackground1 container-background position-fixed top-0 start-0 vw-100 vh-100">
        <img src="images/login-background-1.png" alt="" class="background-image-1 w-100 h-100 object-fit-cover">
        <img src="images/login-background-2.png" alt="" class="background-image-2 object-fit-cover">
        <div class="background-image-3"><img src="images/bgg.png" alt="" class=""></div>
        <div class="background-image-4"><img src="images/Magellan-Logo-w-Tagline.png" alt="" class=""></div>
    </div>
    <!-- <div class="container-background position-fixed top-0 start-0 vw-100 vh-100 bg-black"><img src="images/bgg.png" alt="" class="w-100 h-100 d-block opacity-75 object-fit-cover"></div> -->
    <div class="center-card">
        <div class="main-card">
                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column justify-content-center h-100 p-3">
                <div class="form-header">
                    <div class="form-mainheader">WELCOME TO MAGELLAN SOLUTIONS</div>
                    <div class="form-subheader">Sign In to Visitor Log</div>
                    </div>                    
                    @csrf
                    <!-- EMAIL ADDRESS -->
                    <div class="input-holder">
                        <label for="emp_code" class="">{{ __('Username') }}</label>

                        <div class="">
                        <input id="emp_code" type="text" class="form-control" name="emp_code" placeholder="enter username" required autocomplete="off" autofocus>

                            @error('emp_code')
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
                        <input id="password" type="password" placeholder="enter password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- FORGOT AND LOGIN BUTTON -->
                <div class="login-button">
                    <button type="submit" class="btn w-300px border-0">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
