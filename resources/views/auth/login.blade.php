@extends('layouts.client.client')

@section('content')
<style>

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group .form-control {
    flex-grow: 1;
}

.input-group-append .input-group-text {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0px 10px;
    border: 1px solid #DADADA;
    border-left: none;
    transition: background-color 0.2s, border-color 0.2s;
}
/* When the input is focused */
.input-group:focus-within .input-group-append .input-group-text {
    border-color: #777; /* Match the focus color of the input */
    background-color: #e9ecef; /* Optional: slightly change background on focus */
}
</style>
<!-- register-area -->
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">

        <div class="col-md-6">
            <!-- You can add content here if needed -->
        </div>

        <div class="col-md-6">
            <div class="box-for overflow">
                <div class="col-md-12 col-xs-12 login-blocks" style="padding:25px">
                   

                    <!-- Session Status -->
                    @if (session('status'))
                    <div class="alert alert-success text-center">
                        {{ session('status') }}
                    </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Login failed. Check errors below</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <h2>Login </h2>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', session('email')) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                        <i class="fa fa-eye-slash" id="eyeIcon" style="font-weight: 700;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 d-flex align-items-center flex-wrap" style="margin-top: 20px;">
                            <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                    <label class="form-check-label" for="remember_me">Keep me signed in</label>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-right text-start">
                                @if (Route::has('password.request'))
                                <a class="text-sm text-gray-600" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary btn-block" style="width: 100%;">Log in</button>
                        </div>
                    </form>
                    <br>

                    <h2>Social login :</h2>
                    <p>
                        <a class="login-social" href="#"><i class="fa fa-facebook"></i>&nbsp;Facebook</a>
                        <a class="login-social" href="#"><i class="fa fa-google-plus"></i>&nbsp;Gmail</a>
                        <a class="login-social" href="#"><i class="fa fa-twitter"></i>&nbsp;Twitter</a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the icon class
        eyeIcon.classList.toggle('fa-eye-slash');
        eyeIcon.classList.toggle('fa-eye');
    });
</script>



@endsection