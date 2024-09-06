@extends('layouts.client.client')

@section('content')



<!-- Reset Password -->
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">

        <div class="col-md-6">

        </div>

        <div class="col-md-6" >
            <div class="box-for overflow">
                <div class="col-md-12 col-xs-12 login-blocks" style="padding:25px">
                <p class="text-sm text-gray-600">
                        <h2>Reset Password : </h2> <br/>
                    </p>
                    <!-- Session Status -->
                    @if (session('status'))
                    <div class="mb-4 text-center text-sm text-green-500"
                        style="color:red">
                        {{ session('status') }}
                    </div>
                    @endif
                    <!-- Validation Errors -->
                    @if ($errors->any())
                    <div class="mb-4">
                        <ul class="" style="color:red">
                            @foreach ($errors->all() as $error)
                            <li style="list-style-type: none;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
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
                        <div class="form-group mt-3">
                            <label for="password"> New Password</label>
                            <input id="password" class="form-control" type="password" name="password" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mt-3">
                            <label for="password_confirmation"> Confirm Password</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-default"> Reset Password</button>
                        </div>
                    </form>
                    <br>
                </div>

            </div>
        </div>

    </div>
</div>




@endsection