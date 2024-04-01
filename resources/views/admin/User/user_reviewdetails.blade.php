@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('user') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <h4>Review Details</h4>
    <hr>
    <h4 class="">When you click Save, we will:</h4>
                  
                  <ul class="" >
                    <li>Email instructions to the user telling them how to sign in</li>
                    <li>Use the listed email address as their username</li>
                    <li>Prompt them to choose a password when they try to sign in</li>
           
                  </ul>
    @endif
    @include('admin.CRUD.wizardbuttons')
</form>