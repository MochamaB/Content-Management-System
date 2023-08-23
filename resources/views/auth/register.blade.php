@extends('layouts.client.client')

@section('content')

    <!-- register-area -->
 <div class="register-area" style="background-color: rgb(249, 249, 249);">
            <div class="container">

                <div class="col-md-6">
                   
                </div>

                <div class="col-md-6">
                    <div class="box-for overflow">                         
                        <div class="col-md-12 col-xs-12 login-blocks">
                            <h2>Register : </h2> 
                            
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
                                <p>Contact Agent or Admin to send you an invite Link</p>
                         
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



