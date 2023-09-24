@extends('layouts.admin.admin')

@section('content')


<!----------- Index --------------->
        @if(($routeParts[1] === 'index'))
            @include('admin.CRUD.index')
<!------------ Create------------------->
        @elseif(($routeParts[1] === 'create'))

                @include('admin.CRUD.create')
<!------ Edit---------------->

        @elseif(($routeParts[1] === 'edit'))

                @include('admin.CRUD.edit')
<!------ show---------------->

        @elseif(($routeParts[1] === 'show'))

        @include('admin.CRUD.show')
        @endif

@endsection

@include('layouts.admin.scripts')