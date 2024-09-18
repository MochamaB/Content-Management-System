<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Range Picker Form</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</head>
<body>
    <form id="dateRangeForm" action="{{ url($routeParts[0]) }}" method="GET" style="margin-bottom:0px" class="d-flex justify-content-end">
        <div class="form-group mr-4" style="margin-bottom:0px">
            <input type="text" class="form-control" id="daterange" name="daterange" style="max-width: 250px;" />
        </div>
        <input type="hidden" name="from_date" id="from_date">
        <input type="hidden" name="to_date" id="to_date">
    </form>

    <script>
    $(function() {
        var start = moment().startOf('month');
        var end = moment();

        function cb(start, end) {
            $('#daterange').val(start.format('YYYY MMMM') + ' - ' + end.format('YYYY MMMM'));
            $('#from_date').val(start.format('YYYY-MM-DD'));
            $('#to_date').val(end.format('YYYY-MM-DD'));
        }

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'This Year': [moment().startOf('year'), moment().endOf('year')],
               'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            }
        }, cb);

        cb(start, end);

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $('#dateRangeForm').submit();
        });
    });
    </script>
</body>
</html>