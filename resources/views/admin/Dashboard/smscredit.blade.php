<style>
  .statistics-details {
    display: flex;
    /* Use Flexbox for layout */
    flex-wrap: wrap;
    /* Allow items to wrap if necessary */
  }

  .statistics-details .col {
    display: flex;
    /* Use Flexbox within each column */
    flex-direction: column;
    /* Stack content vertically */
    justify-content: space-between;
    /* Distribute content evenly */
    box-sizing: border-box;
    /* Ensure padding and border are included in width/height */
    min-height: 50px;
    /* Set a minimum height for cards */
  }

  .row .col:not(:first-child) {
    border-left: 2px solid #F4F5F7;
  }

  /* Screen size under 576px (phone) */
  @media (max-width: 576px) {
    .row .col:not(:first-child) {
      border-left: none;
    }

    .row .col:last-child,
    .row .col:nth-last-child(2) {
      border-bottom: none;
      /* Remove border-bottom for last and second-to-last children */
    }
  }
</style>
<div style="padding:30px;background-color: white;border: 1px solid #ccc;margin-top:-4px;">
  @include('admin.CRUD.card_title')
  <div class="row statistics-details " style="margin: 10px 0px; padding:15px 10px 0px 0px; ">
    @foreach($tariffs as $item)
    <!-- TOTAL CARDS -->
    <div class="col text-left pb-2">
      @php
      // Determine the color based on percentage value
      if ($item->available_credits < 50) {
        $color='red' ;
        } elseif ($item->available_credits > 200) {
        $color = 'blue';
        } else {
        $color = 'black'; // Default color for percentages between 50 and 80
        }

        if ($item->credit_type == 1) {
        // Check if property exists
        $creditname = $item->property ? $item->property->property_name : 'No Property';
        } elseif ($item->credit_type == 2) {
        // Check if user exists
        $creditname = $item->user ? $item->user->firstname : 'No User';
        } elseif ($item->credit_type == 3) {
        $creditname = 'The';
        }
        @endphp

        <p class="statistics-title d-flex align-items-bottom">
          <i class="mdi mdi-cash" style="color: #5dc71b; padding-left: 5px;"></i> <!-- Change the color and add padding -->

          <span style="padding-left: 5px;">{{$creditname}} Available Credits</span>
        </p> <!--Title -->

        <h3 class="rate-percentage text-center">
          <span class="text-muted font-weight-medium text-small d-flex align-items-center me-2">
            {{ $sitesettings->site_currency ?? '' }}
          </span>
          {{$item->available_credits }}
        </h3>

    </div>
    @endforeach
    <div class="col text-left pb-2">

      <p class="statistics-title d-flex align-items-bottom">
        <i class="mdi mdi-numeric" style="color: #1F3BB3; padding-left: 5px;"></i> <!-- Change the color and add padding -->

        <span style="padding-left: 5px;">Total Message</span>
      </p> <!--Title -->

      <h3 class="rate-percentage text-center">
        {{$textContent->count() }}
      </h3>

    </div>

  </div>
  <!----  Second Level ------>
  <div class="row statistics-details " style="margin: 10px 0px; padding:15px 10px 0px 0px; ">
    @include('admin.Dashboard.charts.doughnutChart', ['chartData' => $textChartData])

  </div>
</div>