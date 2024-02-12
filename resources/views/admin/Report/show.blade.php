@extends('layouts.admin.admin')

@section('content')

@include('admin.Report.report_filter')


    <div class=" table-responsive" id="dataTable">
        <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
            <thead>
                <tr>
                    @foreach($columns as $column)
                    <th data-sortable="true">{{ $column }}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody>
                @if($data)
                @foreach($data as $row)
                <tr>
                    @foreach($columns as $column)
                    <td>{{ $row[$column] }}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection