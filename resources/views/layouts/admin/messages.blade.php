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
                      <strong>Error! </strong> {{ session('statuserror') }}. 
                        <button type="button" class="btn-danger float-end" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                </div>
				@endif
				
				  @if($errors->all())
            @foreach ($errors->all() as $error)
            <h6 class="alert alert-danger">{{ $error }}</h6>
                @endforeach
            @endif

