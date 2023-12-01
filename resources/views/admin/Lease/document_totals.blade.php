

<div class="row">
    <div class="col-md-6">
    <h4><b>Payment Method </b></h4>
    <p>We Accept M-PESA, Cash</p>
        <ul class="ml-0 px-3 list-unstyled">
            <li>1. Go to the M-PESA Menu </li>
            <li>2. Go to Lipa Na Mpesa </li>
            <li>3. Select Paybill </li>
            <li>4. Enter the business no. <span style="color:blue; font-weight:700;"></span></li>
            <li>5. Enter the Account no. The Invoice Number <span style="color:blue; font-weight:700;">{{$invoice->id}}-{{$invoice->referenceno}}</span></li>
            <li>6. Enter Total amount due. <span style="color:blue; font-weight:700;">{{ $sitesettings->site_currency }} {{$invoice->totalamount}} </span></li>
            <li>7. Complete Transaction</li>
        </ul>
    </div>
   
    <div class="col-md-6 table-responsive">
        <table class="table table-bordered">
            <tbody >
                <tr style="height:45px;">
                    <td>Sub Total Amount</td>
                    <td class="text-center">{{ $sitesettings->site_currency }} {{$invoice->totalamount}}</td>
                </tr>
                <tr style="height:45px;">
                    <td>Tax & Discounts</td>
                    <td class="text-center">{{ $sitesettings->site_currency }} 0 </td>
                </tr>
                <tr style="height:45px;">
                    <td>Other Charges</td>
                    <td class="text-center">{{ $sitesettings->site_currency }} 0 </td>
                </tr>
                <tr style="height:45px;">
                    <td class="text-bold-800" style="font-size:18px;font-weight:700">Total Due</td>

                    <td class="text-bold-800 text-center" style="font-size:18px;font-weight:700">{{ $sitesettings->site_currency }} {{$invoice->totalamount}} </td>

                </tr>
            </tbody>
        </table>
    </div>
</div>