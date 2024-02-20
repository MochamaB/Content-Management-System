@php
use Carbon\Carbon;
@endphp
<div class=" table-responsive" id="dataTable">

    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="false" data-sticky-header="true" data-pagination="false" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="true" data-side-pagination="client" class="table table-bordered" style="padding-left: 15px;">
        <thead>
            <tr>
                @foreach($headers as $header)
                <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($generalLedgerEntries as $entry)
            <tr style="height:40px;">
                <td>{{ $entry['date'] }}</td>
                <td>{{ $entry['account'] }}</td>
                <td>{{ $entry['description'] }}</td>
                <td>{{ $entry['charge_name'] }}</td>
                <!-- Display Debit amount with currency if not null -->
                <td>
                    @if ($entry['debit'] !== null)
                    {{ $sitesettings->site_currency }} {{ number_format($entry['debit'], 0, '.', ',') }}
                    @endif
                </td>
                <!-- Display Credit amount with currency if not null -->
                <td>
                    @if ($entry['credit'] !== null)
                    {{ $sitesettings->site_currency }} {{ number_format($entry['credit'], 0, '.', ',') }}
                    @endif
                </td>

                <td><b>{{ $sitesettings->site_currency }} {{ number_format($entry['balance'], 0, '.', ',') }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>