<!-- Header Section -->
<div class="header">
    <div class="restaurant-name">{{ $settings['restaurant_name'] ?? 'RESTAURANT POS' }}</div>
    
    @if(!empty($settings['business_address']))
        <div class="address">{{ $settings['business_address'] }}</div>
    @endif
    
    @if(!empty($settings['primary_phone']))
        <div class="contact">
            <span>Mobile: {{ $settings['primary_phone'] }}</span>
            @if(!empty($settings['secondary_phone']))
                <span> / {{ $settings['secondary_phone'] }}</span>
            @endif
        </div>
    @endif
    
    @if(!empty($settings['gst_number']))
        <div class="gst">GST: {{ $settings['gst_number'] }}</div>
    @endif
</div>

<div style="border-top: 1px dashed #999; margin: 5px 0;"></div>

<!-- Bill Info -->
<div style="font-size: 9px; margin: 3px 0;">
    <div>Table: {{ $bill->order->restaurantTable->table_number }}</div>
</div>
<div style="font-size: 9px; margin: 3px 0;">
    <div>Bill No: {{ $bill->bill_number }}</div>
</div>
<div style="font-size: 9px; margin: 3px 0;">
    <div>Date: {{ $bill->created_at->format('d/m/Y h:i A') }}</div>
</div>
<div style="font-size: 9px; margin: 3px 0 5px;">
    <div>Status: <span style="text-transform: uppercase;">{{ $bill->status }}</span></div>
</div>

<div style="border-top: 1px dashed #999; margin: 5px 0;"></div>

<!-- Items Table -->
<table class="items-table">
    <thead>
        <tr>
            <th style="text-align: left; width: 50%;">ITEM</th>
            <th style="text-align: center; width: 15%;">QTY</th>
            <th style="text-align: right; width: 20%;">RATE</th>
            <th style="text-align: right; width: 15%;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->order->orderItems as $item)
            <tr>
                <td style="text-align: left; padding: 4px 0;">
                    {{ $item->menuItem->name }}
                    @if($item->notes)
                        <div style="font-size: 8px; color: #666; font-style: italic;">
                            {{ $item->notes }}
                        </div>
                    @endif
                </td>
                <td style="text-align: center; padding: 4px 0;">{{ $item->quantity }}</td>
                <td style="text-align: right; padding: 4px 0;">{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right; padding: 4px 0;">{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="border-top: 1px dashed #999; margin: 5px 0;"></div>

<!-- Totals -->
<table style="width: 100%; font-size: 9px; margin: 5px 0;">
    <tr>
        <td style="text-align: left; padding: 2px 0;">Sub Total:</td>
        <td style="text-align: right; padding: 2px 0;">₹{{ number_format($bill->subtotal, 2) }}</td>
    </tr>
    
    @if($taxAmount > 0)
        <tr>
            <td style="text-align: left; padding: 2px 0;">CGST ({{ number_format($taxRate / 2, 1) }}%):</td>
            <td style="text-align: right; padding: 2px 0;">₹{{ number_format($taxAmount / 2, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 2px 0;">SGST ({{ number_format($taxRate / 2, 1) }}%):</td>
            <td style="text-align: right; padding: 2px 0;">₹{{ number_format($taxAmount / 2, 2) }}</td>
        </tr>
    @endif
    
    @if($serviceCharge > 0)
        <tr>
            <td style="text-align: left; padding: 2px 0;">Service Charge ({{ number_format($serviceChargeRate, 2) }}%):</td>
            <td style="text-align: right; padding: 2px 0;">₹{{ number_format($serviceCharge, 2) }}</td>
        </tr>
    @endif
    
    @php
        $totalQuantity = $bill->order->orderItems->sum('quantity');
    @endphp
    <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 10px;">
        <td style="padding: 4px 0; text-align: left;">
            TOTAL ({{ $totalQuantity }} items)
        </td>
        <td style="padding: 4px 0; text-align: right;">
            ₹{{ number_format($newTotal, 2) }}
        </td>
    </tr>
</table>

<div style="border-top: 1px dashed #999; margin: 5px 0;"></div>

<!-- Payment Info -->
<div style="font-size: 9px; margin: 5px 0;">
    <div style="display: flex; justify-content: space-between; margin: 3px 0;">
        <span>Payment Method:</span>
        <span style="font-weight: bold;">{{ strtoupper($bill->payment_method ?? 'CASH') }}</span>
    </div>
    <div style="display: flex; justify-content: space-between; margin: 3px 0;">
        <span>Amount Paid:</span>
        <span style="font-weight: bold;">₹{{ $bill->status === 'paid' ? number_format($bill->total_amount, 2) : '0.00' }}</span>
    </div>
    @if($bill->paid_at)
        <div style="display: flex; justify-content: space-between; margin: 3px 0;">
            <span>Paid At:</span>
            <span>{{ $bill->paid_at->format('d/m/Y h:i A') }}</span>
        </div>
    @endif
</div>

<div class="footer">
    <div>Thank you for dining with us!</div>
    <div>Visit Again</div>
</div>
