<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Account</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($generalLedgerEntries as $entry)
            <tr>
                <td>{{ $entry['date'] }}</td>
                <td>{{ $entry['description'] }}</td>
                <td>{{ $entry['account'] }}</td>
                <td>{{ $entry['debit'] }}</td>
                <td>{{ $entry['credit'] }}</td>
                <td>{{ $entry['balance'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
