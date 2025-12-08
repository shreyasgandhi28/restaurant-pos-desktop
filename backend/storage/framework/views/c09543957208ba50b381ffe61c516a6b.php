<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #<?php echo e($bill->bill_number); ?></title>
    <style>
        @font-face {
            font-family: 'Noto Sans Devanagari';
            font-style: normal;
            font-weight: normal;
            src: url('<?php echo e(storage_path('fonts/NotoSansDevanagari-Regular.ttf')); ?>') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Devanagari';
            font-style: normal;
            font-weight: bold;
            src: url('<?php echo e(storage_path('fonts/NotoSansDevanagari-Bold.ttf')); ?>') format('truetype');
        }

        @page {
            size: 80mm auto;
            margin: 0;
            padding: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Devanagari', 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
        }
        
        body {
            width: 65mm;
            margin: 0 auto;
            padding: 0;
            padding-bottom: 2mm;
            color: #000;
            font-size: 12px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .header {
            text-align: center;
            margin-bottom: 4px;
            padding-bottom: 4px;
        }
        
        .restaurant-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .address {
            font-size: 12px;
            margin: 2px 0;
            color: #000;
        }
        
        .contact {
            font-size: 12px;
            color: #000;
            margin: 2px 0;
        }
        
        .gst {
            font-size: 12px;
            color: #000;
            margin: 2px 0 6px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .bill-info {
            font-size: 12px;
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
            page-break-inside: auto;
        }
        
        .items-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .items-table th {
            text-align: left;
            padding: 3px 0;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
        }
        
        .items-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 12px;
        }
        
        .items-table .item-name {
            width: 44%;
            padding-right: 0;
        }
        
        .items-table .qty {
            width: 8%;
            text-align: center;
            padding: 0;
        }
        
        .items-table .price {
            width: 22%;
            text-align: right;
            padding-right: 0;
        }
        
        .items-table .total {
            width: 26%;
            text-align: right;
            padding-right: 0;
            padding-left: 15px;
        }
        
        .totals-section {
            margin: 10px 0;
            border-top: 1px dashed #000;
            padding-top: 5px;
            page-break-inside: avoid;
        }
        
        .totals-table {
            width: 100%;
            font-size: 12px;
            page-break-inside: avoid;
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
            font-size: 14px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 5px 0;
        }
        
        .payment-section {
            margin: 10px 0;
            padding: 5px 0;
            border-top: 1px dashed #000;
            font-size: 12px;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .footer {
            text-align: center;
            font-size: 12px;
            color: #000;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }
    </style>
</head>
<body>
    <?php echo $__env->make('bills.partials.receipt', [
        'bill' => $bill,
        'items' => $items,
        'settings' => $settings,
        'taxRate' => $taxRate,
        'taxAmount' => $taxAmount,
        'serviceChargeRate' => $serviceChargeRate,
        'serviceCharge' => $serviceCharge,
        'newTotal' => $newTotal
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/bills/pdf.blade.php ENDPATH**/ ?>