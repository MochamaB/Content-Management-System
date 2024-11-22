<!-- Session Status -->
@if (session('status'))
<div class="alert alert-success" role="alert">
    <i class="menu-icon mdi mdi mdi-check-circle mdi-24px"></i>
    <strong>Sucess! </strong> {{ session('status') }}.
    <button type="button" class="btn-success float-end" style="float:right;color:#fff" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($errors->all())
<div class="alert alert-danger" role="alert">
  <button type="button" class="btn-danger float-end" style="float:right;color:#fff" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <ul>
    @foreach ($errors->all() as $error)
    <li class="" style="list-style-type: none;"> <i class="menu-icon mdi mdi mdi-alert-circle mdi-12px"></i> <strong>Error! </strong>{{ $error }}</li>
    @endforeach
  </ul>

</div>
@endif
           