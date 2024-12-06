  <div class="table-responsive  mt-1">
    <table class="table select-table">
      <thead>
        <tr>
          @foreach($data['headers'] as $index => $header)
          <th>{{$header}}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($data['rows'] as $key => $row)
        <tr>
          @foreach($row as $cell)
          <td>{!! $cell !!}</td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>