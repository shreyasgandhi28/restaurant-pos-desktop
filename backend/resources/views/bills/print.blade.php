<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #{{ $bill->bill_number }}</title>
    <style>
        @font-face {
            font-family: 'Noto Sans Devanagari';
            font-style: normal;
            font-weight: normal;
            src: url('{{ asset('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Devanagari';
            font-style: normal;
            font-weight: bold;
            src: url('{{ asset('fonts/NotoSansDevanagari-Bold.ttf') }}') format('truetype');
        }

        @page {
            size: 80mm 297mm;
            margin: 0;
            padding: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Devanagari', 'Arial', 'Helvetica', sans-serif;
            line-height: 1.2;
        }
        
        body {
            width: 70mm;
            margin: 0 auto;
            padding: 5mm;
            color: #000;
            font-size: 9px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .header {
            text-align: center;
            margin-bottom: 4px;
            padding-bottom: 4px;
        }
        
        .restaurant-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .address {
            font-size: 9px;
            margin: 2px 0;
            color: #333;
        }
        
        .contact {
            font-size: 9px;
            color: #333;
            margin: 2px 0;
        }
        
        .gst {
            font-size: 9px;
            color: #333;
            margin: 2px 0 6px;
        }
        
        .divider {
            border-top: 1px dashed #999;
            margin: 5px 0;
        }
        
        .bill-info {
            font-size: 9px;
            margin: 2px 0;
        }
        
        .bill-info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        
        .items-table th {
            text-align: left;
            padding: 3px 0;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 9px;
        }
        
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 9px;
        }
        
        .items-table .item-name {
            width: 50%;
            padding-right: 5px;
        }
        
        .items-table .qty {
            width: 15%;
            text-align: center;
        }
        
        .items-table .price {
            width: 20%;
            text-align: right;
            padding-right: 5px;
        }
        
        .items-table .total {
            width: 15%;
            text-align: right;
        }
        
        .totals-section {
            margin: 10px 0;
            border-top: 1px dashed #999;
            padding-top: 5px;
        }
        
        .totals-table {
            width: 100%;
            font-size: 9px;
        }
        
        .totals-table td {
            padding: 2px 0;
        }
        
        .totals-table .label {
            text-align: left;
        }
        
        .totals-table .amount {
            text-align: right;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 5px 0;
        }
        
        .payment-section {
            margin: 10px 0;
            padding: 5px 0;
            border-top: 1px dashed #999;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #999;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Setting::all()->keyBy('key')->map(function($item) {
            return $item->value;
        });
        
        $bill->load(['order.orderItems.menuItem', 'order.restaurantTable']);
        
        $taxRate = $bill->order->tax_rate;
        $serviceChargeRate = $bill->order->service_charge_rate;
        $taxAmount = $bill->subtotal * ($taxRate / 100);
        $serviceCharge = $bill->subtotal * ($serviceChargeRate / 100);
        $newTotal = $bill->subtotal + $taxAmount + $serviceCharge - $bill->discount_amount;
        $totalQuantity = $bill->order->orderItems->sum('quantity');
    @endphp

    @include('bills.partials.receipt', [
        'bill' => $bill,
        'settings' => $settings,
        'taxRate' => $taxRate,
        'taxAmount' => $taxAmount,
        'serviceChargeRate' => $serviceChargeRate,
        'serviceCharge' => $serviceCharge,
        'newTotal' => $newTotal
    ])

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
