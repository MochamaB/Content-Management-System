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
                    <div class="col-md-2 " style="padding-top:0px" >

                        <button class="btn btn-warning btn-lg text-white" id="filter" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fas fa-plus" id="collapseIcon"></i> Advanced Filter <i class="fas fa-times" id="expandIcon" style="display: none;"></i>
                        </button>
                    </div>


                    <div class="collapse" id="collapseExample" style="margin-bottom:20px;">
                        <div class="filterbody">
                            <!-- Add the button here to close the collapse div -->

                            <button class="btn-success float-end" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <!-- resources/views/master_filter.blade.php -->

                            <div class="row">
                                <div class="row">
                                @if (isset($filters))
                                    @foreach ($filters as $filter => $items)
                                        @if ($items['inputType'] === 'selectgroup')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="label">{{ $items['label'] }}</label>
                                                    <select class="formcontrol2" id="{{ $filter }}" onchange="applyFilters('{{ $filter }}')">
                                                        @foreach ($data[$filter] as $groupLabel => $items)
                                                        <optgroup label="{{ $groupLabel }}">
                                                            @foreach ($items as $item)
                                                            <option value="{{ $item }}">{{ $item }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @elseif ($items['inputType'] === 'select')
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="label">{{ $items['label'] }}</label>
                                                    <select name="{{ $filter }}" class="formcontrol2" id="{{ $filter }}" onchange="applyFilters('{{ $filter }}')">
                                                        <option value="All">All</option>
                                                        @foreach ($data[$filter] as $item)
                                                        <option value="{{ $item }}">{{ $item }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="col-md-12">
                                        <h4>Filter not available.</h4>
                                    </div>
                                @endif
                                </div>
                            </div>



                        </div>
                    </div>