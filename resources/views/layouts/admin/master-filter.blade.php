                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="controller" id="controller" class="formcontrol2" onchange="applyFilters('controller')">
                                <option value="" disabled selected>FILTER</option>
                                <option value="All">ALL </option>
                                @if(isset($mainfilter) && $mainfilter !== null)
                                @foreach ($mainfilter as $item)
                                <option value="{{ $item ?? '' }}" style="text-transform: capitalize;">{{ $item ?? '' }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                    <div class="col-md-2 " style="padding-top:0px">

                        <button class="btn btn-warning btn-lg text-white" id="filter" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fas fa-plus" id="collapseIcon"></i> Advanced Filter <i class="fas fa-times" id="expandIcon" style="display: none;"></i>
                        </button>
                    </div>


                    <div class="collapse" id="collapseExample" style="margin-bottom:20px;">
                        <div class="filterbody">
                            <form method="GET" action="{{ url()->current() }}">
                                <div class="row">

                                    @if (isset($filterdata))
                                    @foreach($filterdata as $key => $filter)
                                    <div class="col-md-3" style="padding:0px 5px 0px 8px;">
                                        <div class="form-group" style="margin-bottom: 0.5rem;">
                                            <label class="label">{{ $filter['label'] }}</label>

                                            @if($filter['inputType'] == 'select')
                                            <!------  SELECT------------>
                                            <select class="formcontrol2" name="{{ $key }}" id="{{ $key }}">

                                                <option value="">All {{ $filter['label'] }}</option>
                                                @foreach ($filter['values'] as $id => $value)
                                                <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <!---- GROUP SELECT ------------->
                                            @elseif($filter['inputType'] === 'selectgroup')
                                            <select class="formcontrol2" id="{{ $key }}" name="{{ $key }}">
                                                <option value="">All {{ $filter['label'] }}</option>
                                                @foreach ($filter['values'] as $groupKey => $groupValues)
                                                <optgroup label="{{ $groupKey }}">
                                                    @foreach ($groupValues as $id => $value)
                                                    <option value="{{ $id }}" {{ request($key) == $id ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </optgroup>
                                                @endforeach
                                            </select>

                                            <!--------  DATE -------------->
                                            @elseif($filter['inputType'] === 'date')
                                            <input type="date" class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ request($key) }}" />
                                            @endif

                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="col-md-3 ms-auto text-end" style="padding:35px 10px 0px 10px">
                                        <button type="submit" class="btn btn-primary btn-lg text-white mt-0 me-0 nextbutton">Apply Filter</button>
                                    </div>


                                    @else
                                    <div class="col-md-12">
                                        <h4>Filter not available.</h4>
                                    </div>
                                    @endif
                                </div>
                            </form>

                        </div>
                    </div>