<form  id="dateRangeForm" class="filterForm" method="GET" action="{{ url()->current() }}">
    @include('admin.CRUD.card_title')
</form>
               
@if(isset($dashboardConfig)) <!-- ONE CARD COVERS WHOLE AREA --->
            @include('admin.Dashboard.card_section')
        
@endif
