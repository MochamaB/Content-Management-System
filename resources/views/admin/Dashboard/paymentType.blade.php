<style>
    .payment-circle-primary {
        width: 8px;
        height: 8px;
        border: 3px solid #1F3BB3;
        border-radius: 100%;
        position: relative;
    }

    .modern-payment .more-options-payment {
        font-weight: 700;
        font-size: 14px;
        color: #1F3BB3;
        text-decoration: none;
        margin-top: 10px;
    }

    .modern-payment .payment-amount {
        font-weight: 700;
        font-size: 15px;
        color: #000000;
    }
</style>
<div class="row flex-grow">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body  modern-payment">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">Total Payments Per Charge</h4>
                    </div>
                </div>
                @foreach ($paymentType as $payment)
                <div class="wrapper d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center">
                        <div class="payment-circle-primary"></div>
                        <div class="wrapper mb-0 ms-3">
                            <p class="payment-title mb-0">{{ $payment['name'] }}</p>
                        </div>
                    </div>
                    <div class="payment-amount">
                        {{ $sitesettings->site_currency }} {{ number_format($payment['total_payments'], 0) }}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer modern-payment">
                <a href="{{ url('/payments')}}" class="more-options-payment d-flex align-items-center">More Payments <i class="mdi mdi-chevron-right"></i></a>
            </div>
        </div>
    </div>
</div>