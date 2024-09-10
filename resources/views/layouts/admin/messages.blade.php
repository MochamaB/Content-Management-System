@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="menu-icon mdi mdi mdi-check-circle mdi-24px"></i>
  <strong>Sucess! </strong> {{ session('status') }}.
  <button type="button" class="btn-success float-end" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if (session('statuserror'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="menu-icon mdi mdi mdi-alert-circle mdi-24px"></i>
  <strong>Error! </strong> {!! session('statuserror') !!}.
  <button type="button" class="btn-danger float-end" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if (isset($info))
<div class="alert alert-info alert-dismissible fade show" role="alert">
  <i class="menu-icon mdi mdi mdi-alert-circle mdi-24px"></i>
  <strong>Information!  </strong>  {!! $info !!}.
  <button type="button" class="btn-info float-end" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if($errors->all())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <button type="button" class="btn-danger float-end" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <ul>
    @foreach ($errors->all() as $error)
    <li class="" style="list-style-type: none;"> <i class="menu-icon mdi mdi mdi-alert-circle mdi-12px"></i> <strong>Validation Error! </strong>{{ $error }}</li>
    @endforeach
  </ul>

</div>
@endif