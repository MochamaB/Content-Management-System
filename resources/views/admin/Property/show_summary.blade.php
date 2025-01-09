<div class="btn-wrapper d-flex align-items-center justify-content-end">
    <h3>Overview</h3>
    @include('admin.CRUD.card_title')
        
        </div>
@if(isset($dashboardConfig)) <!-- ONE CARD COVERS WHOLE AREA --->
            @include('admin.Dashboard.card_section')
        
    @endif