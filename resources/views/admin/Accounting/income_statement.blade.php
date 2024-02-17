@php
use Carbon\Carbon;
@endphp
<div class=" table-responsive" id="dataTable">

    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="false" data-sticky-header="true" data-pagination="false" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="true" data-side-pagination="client" class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                @foreach($months as $month)
                <th>{{ $month }}</th>
                @endforeach
                <th>Totals</th>
            </tr>
        </thead>
        <tbody>
            @php
            $grandTotalIncome = 0;
            $grandTotalExpense = 0;
            @endphp
            <!-----  Income ----------------------->
            <!------------  Expenses -------->
            <tr style="height:40px;">
                <th style="font-weight: bold; font-size:15px;color:#1F3BB3">INCOME</th>
            </tr>
            @foreach($incomeTransactions->groupBy('creditaccount_id') as $creditAccountId => $transactions)
            <tr style="height:40px;">
                <td style="font-weight:400;">{{ $transactions->first()->description }}</td>
                @php
                $accountTotalIncome = 0;
                @endphp

                @foreach($months as $month)
                @php
                $monthTotalIncome = $transactions->where('month', $month)->sum('total');
                $accountTotalIncome += $monthTotalIncome;
                $grandTotalIncome += $monthTotalIncome;
                @endphp

                <td>{{ $sitesettings->site_currency }}  {{ number_format($monthTotalIncome, 0, '.', ',') }}</td>
                @endforeach

                <td><b>
                        {{ $sitesettings->site_currency }}  {{ number_format($accountTotalIncome, 0, '.', ',') }}</td>
                </b>
            </tr>
            @endforeach
            <!---- Income Totals ------------->
            <tr style="height:40px;font-weight: bold;">
                <td style = "font-weight: bold; font-size: 15px;">Total Income</td>
                @foreach($months as $month)
                @php
                $totalIncomeMonth = $incomeTransactions->where('month', $month)->sum('total');
                @endphp
                <td>{{ $sitesettings->site_currency }}  {{ number_format($totalIncomeMonth, 0, '.', ',') }}</td>
                @endforeach
                <td> {{ $sitesettings->site_currency }} {{ number_format($grandTotalIncome, 0, '.', ',') }}</td>
            </tr>
            <!------------  Expenses -------->
            <tr style="height:40px;">
                <th style="font-weight: bold; font-size:15px;color:#1F3BB3">EXPENSES</th>
            </tr>

            @foreach($expenseTransactions->groupBy('creditaccount_id') as $creditAccountId => $transactions)
            <tr style="height:40px;">
                <td style="font-weight:400;">{{ $transactions->first()->description }}</td>
                @php
                $accountTotalExpense = 0;
                @endphp

                @foreach($months as $month)
                @php
                $monthTotalExpense = $transactions->where('month', $month)->sum('total');
                $accountTotalExpense += $monthTotalExpense;
                $grandTotalExpense += $monthTotalExpense;
                @endphp

                <td>{{ $sitesettings->site_currency }}  {{ number_format($monthTotalExpense, 0, '.', ',') }}</td>
                @endforeach

                <td><b>
                        {{ $sitesettings->site_currency }}  {{ number_format($accountTotalExpense, 0, '.', ',') }}</td>
                </b>
            </tr>
            @endforeach
            <!----  Expense Totals ----------->
            <tr style="height:40px;font-weight: bold;">
                <td style="font-weight: bold; font-size: 15px;">Total Expenses</td>
                @foreach($months as $month)
                @php
                $totalExpenseMonth = $expenseTransactions->where('month', $month)->sum('total');
                @endphp
                <td>{{ $sitesettings->site_currency }}  {{ number_format($totalExpenseMonth, 0, '.', ',') }}</td>
                @endforeach
                <td> {{ $sitesettings->site_currency }} {{ number_format($grandTotalExpense, 0, '.', ',') }}</td>
            </tr>
            <tr style="height: 45px; background-color:#F4F5F7;;">
                <td style="font-weight: bold; font-size: 16px;">Net Profit</td>
                @foreach($months as $month)
                @php
                $totalIncomeMonth = $incomeTransactions->where('month', $month)->sum('total');
                $totalExpenseMonth = $expenseTransactions->where('month', $month)->sum('total');
                $totalProfitMonth = $totalIncomeMonth - $totalExpenseMonth;
                @endphp
                <td style="font-weight: bold; font-size: 16px;">{{ $sitesettings->site_currency }}  {{ number_format($totalProfitMonth, 0, '.', ',') }}</td>
                @endforeach
                <td style="font-weight: bold; font-size: 16px;">{{ $sitesettings->site_currency }} {{ number_format($grandTotalIncome - $grandTotalExpense, 0, '.', ',') }}</td>
            </tr>

        </tbody>
       
    </table>
    <!--- ------------------------------- --------------------- Expense ---------->



</div>