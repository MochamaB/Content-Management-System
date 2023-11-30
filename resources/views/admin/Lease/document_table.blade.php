<div class="table-responsive">
                <table class="table" id="table" data-toggle="table" data-side-pagination="server" data-click-to-select="true" class="table table-hover table-striped" style="font-size:12px;border:1px solid black;">
                    <thead style="" class="sticky-header">
                        <tr class="tableheading">

                            <th>No.</th>
                            <th class="text-center">Description </th>
                            <th class="text-center">Amount Due </th>
                            <th class="text-center">Amount Paid</th>
                            <th class="text-center">Total Due</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach($invoice->invoiceItems as $key=> $item)
                        <tr style="height:35px;">
                            <td class="text-center">{{$key+1}}</td>
                            <td class="text-center">{{$item->charge_name}} </td>
                            <td class="text-center">{{$item->amount}} </td>
                            <td class="text-center"> </td>
                            <td class="text-center"> {{$item->amount}}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div></br>