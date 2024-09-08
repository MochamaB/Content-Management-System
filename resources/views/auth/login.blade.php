@extends('layouts.client.client')

@section('content')

    <!-- register-area -->
 <div class="register-area" style="background-color: rgb(249, 249, 249);">
            <div class="container">

                <div class="col-md-6">
                   
                </div>

                <div class="col-md-6">
                    <div class="box-for overflow">                         
                        <div class="col-md-12 col-xs-12 login-blocks" style="padding:25px">
                            <h2>Login : </h2> 
                            
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
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password"  name="password" required autocomplete="current-password"/>
                                </div>
                                <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a class="text-sm text-gray-600" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif
                                &nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-default"> Log in</button>
                                </div>
                            </form>
                            <br>
                            
                            <h2>Social login :  </h2> 
                            
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

@endsection



