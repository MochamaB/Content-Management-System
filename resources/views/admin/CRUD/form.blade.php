@extends('layouts.admin.admin')

@section('content')


<!----------- Index --------------->
        @if(($routeParts[1] === 'index'))
            @include('admin.CRUD.index')
<!------------ Create------------------->
        @elseif(($routeParts[1] === 'create'))
        <div class=" contwrapper">
                @include('admin.CRUD.create')
        </div>
<!------ Edit---------------->

        @elseif(($routeParts[1] === 'edit'))
        <div class=" contwrapper">
                @include('admin.CRUD.edit')
        </div>
<!------ show---------------->

        @elseif(($routeParts[1] === 'show'))

        @include('admin.CRUD.show')

<!---- Tenants ------------------>
        @elseif(($routeParts[0] === 'tenant'))
        @include('admin.CRUD.index')

<!--- Show-Index -->
        @else
        @endif

@endsection
