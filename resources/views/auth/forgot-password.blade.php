
@extends('layouts.client.client')

@section('content')



 <!-- Forgot Password -->
 <div class="register-area" style="background-color: rgb(249, 249, 249);">
            <div class="container">

                <div class="col-md-6">
                   
                </div>

                <div class="col-md-6">
                    <div class="box-for overflow">                         
                        <div class="col-md-12 col-xs-12 login-blocks">
                        <p class="text-sm text-gray-600">
                        <h2>Reset Password : </h2> <br/>
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
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
                                        <div class="font-medium text-red-600" style="color:red">
                                            {{ __('Whoops! Something went wrong.') }}
                                        </div>

                                        <ul class="" style="color:red">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus>
                                </div>
                            
                                <div class="text-center">
                                    <button type="submit" class="btn btn-default"> Email Password Reset Link</button>
                                </div>
                            </form>
                            <br>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>      




@endsection


