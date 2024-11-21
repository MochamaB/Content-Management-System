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
        border-color: #777;
        /* Match the focus color of the input */
        background-color: #e9ecef;
        /* Optional: slightly change background on focus */
    }
</style>


<!-- Reset Password -->
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">

        <div class="col-md-6">

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
                        <strong>Password reset failed. Check errors below</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <h2>Reset Password : </h2>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email"> Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus readonly>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control password" id="password" name="password" required autocomplete="current-password">
                                <div class="input-group-append">
                                    <span class="input-group-text togglePassword" id="togglePassword" style="cursor: pointer;">
                                        <i class="fa fa-eye-slash eyeIcon" id="eyeIcon" style="font-weight: 700;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control password" id="password_confirmation" name="password_confirmation" required>
                                <div class="input-group-append">
                                    <span class="input-group-text togglePassword" id="togglePassword" style="cursor: pointer;">
                                        <i class="fa fa-eye-slash eyeIcon" id="eyeIcon" style="font-weight: 700;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary btn-block" style="width: 100%;">Reset Password</button>
                        </div>
                    </form>
                    <br>
                </div>

            </div>
        </div>

    </div>
</div>
<script>
  document.querySelectorAll('.togglePassword').forEach(togglePassword => {
    togglePassword.addEventListener('click', function () {
        // Get the password input related to the clicked toggle
        const passwordInput = this.parentElement.previousElementSibling;
        const eyeIcon = this.querySelector('.eyeIcon');

        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the icon class
        eyeIcon.classList.toggle('fa-eye-slash');
        eyeIcon.classList.toggle('fa-eye');
    });
});

</script>




@endsection