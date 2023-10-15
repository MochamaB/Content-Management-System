@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))

    @include('admin.CRUD.formwizard')


@elseif(($routeParts[1] === 'edit'))
    <form method="POST" action="{{ url($routeParts[0].'/'.$user->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @csrf
      
        @include('admin.CRUD.tabs_vertical')
    </form>
@endif

@endsection
<script>
        $(document).ready(function () {
            // Initially hide the Property Access tab
            $(".propertyaccess").hide();

            // Listen for changes in the "role" select input
            $("#role").on("change", function () {
                // Get the selected role
                var selectedRole = $(this).val();

                // Check if the selected role is "Tenant"
                if (selectedRole === "Tenant") {
                    // Hide the Property Access tab
                    $("#property-access").hide();
                } else {
                    // Show the Property Access tab
                    $("#property-access").show();
                }
            });
        });
    </script>