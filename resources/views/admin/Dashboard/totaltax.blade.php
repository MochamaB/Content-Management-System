
<div class="row flex-grow">
    <div class="col-12 grid-margin stretch-card">
        <!-- TICKETS ----------------->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title card-title-dash">Tax Summary</h4>
                        </div>
                        <div class="list-wrapper">
                        @foreach ($taxSummary as $summary)
                            <ul class="todo-list todo-list-rounded">

                           
                                <li class="d-block">
                                    <div class="form-check w-100 mt-1 mb-2">
                                        <label class="form-check-label">
                                            <b>{{$summary['category'] }} Properties</b>
                                           
                                        </label>
                                        @foreach ($summary['taxes'] as $tax)
                                        <div class="d-flex mt-0 mb-2">
                                            <div class="ps-5 me-3">
                                               <p>{{ $tax['tax_name'] }}</p>
                                            </div>
                                            <div class="ps-4  text-muted me-3">
                                            <p>{{ $sitesettings->site_currency }}. {{ $tax['tax_amount'] }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </li>
                            </ul>
                            @endforeach
                            <div class="text-center mt-4">
                                <h5 style="font-weight: 600;">
                                Total Amount &nbsp;<span class="text-primary">{{ $sitesettings->site_currency }}. {{ array_sum(array_column($taxSummary, 'total_tax_amount')) }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>