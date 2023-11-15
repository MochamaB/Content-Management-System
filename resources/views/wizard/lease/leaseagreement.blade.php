@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('lease') }}" class="myForm" enctype="multipart/form-data" novalidate>
@csrf

@include('admin.CRUD.wizardbuttons')
</form>
@endif