<!-- Your Blade View -->

<table border="1">
    <thead>
        <tr>
            <th>Account Name</th>
            @foreach($months as $month)
                <th>{{ $month }}</th>
            @endforeach
            <th>Totals</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotal = 0;
        @endphp

        @foreach($incomeTransactions->groupBy('creditaccount_id') as $creditAccountId => $transactions)
            <tr>
                <td>{{ $transactions->first()->description }}</td>
                @php
                    $accountTotal = 0;
                @endphp

                @foreach($months as $month)
                    @php
                        $monthTotal = $transactions->where('month', $month)->sum('total');
                        $accountTotal += $monthTotal;
                        $grandTotal += $monthTotal;
                    @endphp

                    <td>{{ $monthTotal }}</td>
                @endforeach

                <td>{{ $accountTotal }}</td>
            </tr>
        @endforeach

        <tr>
            <td>Totals</td>
            @foreach($months as $month)
                <td>{{ $incomeTransactions->where('month', $month)->sum('total') }}</td>
            @endforeach
            <td>{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>
