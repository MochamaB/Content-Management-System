
<div class="row align-items-center">
        <div class="col-3">
        </div>
        <div class="col-9" style="margin-bottom:0px">
            <form action="{{  url($routeParts[0]) }}" method="GET" style="margin-bottom:0px" class="d-flex justify-content-end">
                <div class="form-group mr-4" style="margin-bottom:0px">
                    <label class="label" for="month">Month:</label>
                    <select name="month" id="month" class="formcontrol2">
                            <option value="ALL">-- ALL --</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ request('month', date('n')) == $month ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-4" style="margin-bottom:0px">
                    <label class="label" for="year">Year:</label>
                    <select name="year" id="year" class="formcontrol2">
                        <option value="ALL">-- ALL --</option>
                        @foreach(range(date('Y'), 2020, -1) as $year)
                            <option value="{{ $year }}" {{ request('year', date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary text-white mb-0 me-0" style="height:53px;margin-top:30px;margin-bottom:0px;">
                <i class="mdi mdi-filter mdi-24" style="font-size: 17px;color:white"> Filter</i>
            </form>
        </div>
    </div>
   
<hr>