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
        <div style="border-top: 1px dashed #999; margin: 5px 0;"></div>
    @endif
</div>

<!-- Bill Info -->
<div style="display: flex; justify-content: space-between; margin: 5px 0; font-size: 9px;">
    <div>Table: {{ $bill->order->restaurantTable->table_number }}</div>
    <div>Bill No: {{ $bill->bill_number }}</div>
</div>
<div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 9px;">
    <div>Date: {{ $bill->created_at->format('d/m/Y h:i A') }}</div>
    <div>Status: <span style="text-transform: uppercase;">{{ $bill->status }}</span></div>
</div>

<!-- Items Table -->
<table class="items-table">
    <thead>
        <tr>
            <th class="item-name">ITEM</th>
            <th class="qty">QTY</th>
            <th class="price">RATE</th>
            <th class="total">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->order->orderItems as $item)
            <tr>
                <td class="item-name">
                    {{ $item->menuItem->name }}
                    @if($item->notes)
                        <div style="font-size: 8px; color: #666; font-style: italic;">
                            {{ $item->notes }}
                        </div>
                    @endif
                </td>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="price">{{ number_format($item->unit_price, 2) }}</td>
                <td class="total">{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Totals -->
<table style="width: 100%; margin: 10px 0; border-top: 1px dashed #999; padding-top: 5px;">
    <tr>
        <td style="text-align: left;">Sub Total:</td>
        <td style="text-align: right;">{{ number_format($bill->subtotal, 2) }}</td>
    </tr>
    
    @if($taxAmount > 0)
        <tr>
            <td style="text-align: left;">CGST ({{ $taxRate / 2 }}%):</td>
            <td style="text-align: right;">{{ number_format($taxAmount / 2, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: left;">SGST ({{ $taxRate / 2 }}%):</td>
            <td style="text-align: right;">{{ number_format($taxAmount / 2, 2) }}</td>
        </tr>
    @endif
    
    @if($serviceCharge > 0)
        <tr>
            <td style="text-align: left;">Service Charge ({{ $serviceChargeRate }}%):</td>
            <td style="text-align: right;">{{ number_format($serviceCharge, 2) }}</td>
        </tr>
    @endif
    
    @if($bill->discount_amount > 0)
        <tr>
            <td style="text-align: left; color: #dc2626;">
                Discount @if($bill->discount_percentage > 0)({{ $bill->discount_percentage }}%)@endif:
            </td>
            <td style="text-align: right; color: #dc2626;">
                -{{ number_format($bill->discount_amount, 2) }}
            </td>
        </tr>
    @endif
    
    @php
        $totalQuantity = $bill->order->orderItems->sum('quantity');
    @endphp
    <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 11px;">
        <td style="padding: 3px 0; text-align: left;">
            TOTAL ({{ $totalQuantity }} items)
        </td>
        <td style="padding: 3px 0; text-align: right;">
            ₹{{ number_format($newTotal, 2) }}
        </td>
    </tr>
</table>

<!-- Payment Info -->
<div style="margin: 10px 0; padding: 5px 0; border-top: 1px dashed #999;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
        <span>Payment Method:</span>
        <span style="font-weight: 600;">{{ strtoupper($bill->payment_method ?? 'CASH') }}</span>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
        <span>Amount Paid:</span>
        <span style="font-weight: 600;">{{ $bill->status === 'paid' ? number_format($bill->total_amount, 2) : '0.00' }}</span>
    </div>
    @if($bill->paid_at)
        <div style="text-align: right; font-size: 8px; color: #666; margin-top: 2px;">
            {{ $bill->paid_at->format('d/m/Y h:i A') }}
        </div>
    @endif
</div>

<div class="footer">
    <div>Thank you for dining with us!</div>
    <div>Visit Again</div>
</div>
