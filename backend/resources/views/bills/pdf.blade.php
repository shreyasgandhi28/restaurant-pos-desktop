<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Bill #{{ $bill->bill_number }}</title>
    <style>
        @page {
            size: 80mm 297mm; /* Standard A4 height to prevent cutting */
            margin: 0;
            padding: 0;
        }
        
        @font-face {
            font-family: 'Noto Sans Devanagari';
            src: url('{{ storage_path('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Devanagari', 'Arial', 'Helvetica', sans-serif;
            line-height: 1.2;
        }
        
        body {
            width: 70mm; /* Reduced from 80mm to account for padding */
            margin: 0 auto;
            padding: 5mm;
            color: #000;
            font-size: 9px; /* Slightly smaller font */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            max-height: 100%;
            overflow: hidden;
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
        
        .bill-header {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin: 4px 0;
            padding-bottom: 4px;
            border-bottom: 1px dashed #999;
        }
        
        .bill-info {
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            margin: 2px 0 6px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 9px;
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
            text-align: right;
            padding-right: 10px;
        }
        
        .items-table .price {
            width: 20%;
            text-align: right;
            padding-right: 10px;
        }
        
        .items-table .total {
            width: 15%;
            text-align: right;
            font-weight: bold;
        }
        
        .totals-table {
            width: 100%;
            margin: 8px 0;
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
            font-weight: bold;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 5px 0;
        }
        
        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #999;
        }
        
        .thank-you {
            text-align: center;
            font-size: 9px;
            margin: 5px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    @include('bills.partials.receipt')
</body>
</html>
