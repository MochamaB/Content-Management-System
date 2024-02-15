@php
use Carbon\Carbon;
@endphp
<div class=" table-responsive" id="dataTable">

    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="false" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
        <thead>
            <tr>
                <th data-sortable="true">Income</th>

                @foreach ($incomeTransactions->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('F Y');
                })->sortByDesc(function ($date) {
                return $date;
                }) as $month => $transactionsInMonth)
                <th>{{ $month }}</th>
                @endforeach
                <th data-sortable="true">Total</th>
            </tr>
        </thead>
        <tbody>

            @foreach($incomeTransactions->groupBy('creditaccount_id') as $creditAccountId => $transaction)
            <tr>
                <td style="text-transform: capitalize;padding-left: 15px; padding-right:15px;">
                    {{ $transaction->firstWhere('creditaccount_id', $creditAccountId)->description }}
                </td>
                @foreach($transaction->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
                })->sortByDesc(function ($date) {
                return $date;
                }) as $month => $items)
                <td style="text-transform: capitalize;padding-left: 15px; padding-right:15px;">
                    {{$sitesettings->site_currency}} {{$items->sum('amount')}}
                   
                </td>
                @endforeach

                <td style="text-transform: capitalize;padding-left: 15px; padding-right:15px;">
                    {{ $sitesettings->site_currency }} {{ $transaction->sum('amount') }}
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>



</div>