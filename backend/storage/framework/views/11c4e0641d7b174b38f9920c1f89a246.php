<!-- Header Section -->
<div class="header">
    <div class="restaurant-name"><?php echo e($settings['restaurant_name'] ?? 'RESTAURANT POS'); ?></div>
    
    <?php if(!empty($settings['business_address'])): ?>
        <div class="address"><?php echo e($settings['business_address']); ?></div>
    <?php endif; ?>
    
    <?php if(!empty($settings['primary_phone'])): ?>
        <div class="contact">
            <span>Mobile: <?php echo e($settings['primary_phone']); ?></span>
            <?php if(!empty($settings['secondary_phone'])): ?>
                <span> / <?php echo e($settings['secondary_phone']); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($settings['gst_number'])): ?>
        <div class="gst">GST: <?php echo e($settings['gst_number']); ?></div>
        <div style="border-top: 1px dashed #999; margin: 5px 0;"></div>
    <?php endif; ?>
</div>

<!-- Bill Info -->
<div style="display: flex; justify-content: space-between; margin: 5px 0; font-size: 9px;">
    <div>Table: <?php echo e($bill->order->restaurantTable->table_number); ?></div>
    <div>Bill No: <?php echo e($bill->bill_number); ?></div>
</div>
<div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 9px;">
    <div>Date: <?php echo e($bill->created_at->format('d/m/Y h:i A')); ?></div>
    <div>Status: <span style="text-transform: uppercase;"><?php echo e($bill->status); ?></span></div>
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
        <?php $__currentLoopData = $bill->order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="item-name">
                    <?php echo e($item->menuItem->name); ?>

                    <?php if($item->notes): ?>
                        <div style="font-size: 8px; color: #666; font-style: italic;">
                            <?php echo e($item->notes); ?>

                        </div>
                    <?php endif; ?>
                </td>
                <td class="qty"><?php echo e($item->quantity); ?></td>
                <td class="price"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                <td class="total"><?php echo e(number_format($item->total_price, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<!-- Totals -->
<table style="width: 100%; margin: 10px 0; border-top: 1px dashed #999; padding-top: 5px;">
    <tr>
        <td style="text-align: left;">Sub Total:</td>
        <td style="text-align: right;"><?php echo e(number_format($bill->subtotal, 2)); ?></td>
    </tr>
    
    <?php if($taxAmount > 0): ?>
        <tr>
            <td style="text-align: left;">CGST (<?php echo e($taxRate / 2); ?>%):</td>
            <td style="text-align: right;"><?php echo e(number_format($taxAmount / 2, 2)); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">SGST (<?php echo e($taxRate / 2); ?>%):</td>
            <td style="text-align: right;"><?php echo e(number_format($taxAmount / 2, 2)); ?></td>
        </tr>
    <?php endif; ?>
    
    <?php if($serviceCharge > 0): ?>
        <tr>
            <td style="text-align: left;">Service Charge (<?php echo e($serviceChargeRate); ?>%):</td>
            <td style="text-align: right;"><?php echo e(number_format($serviceCharge, 2)); ?></td>
        </tr>
    <?php endif; ?>
    
    <?php if($bill->discount_amount > 0): ?>
        <tr>
            <td style="text-align: left; color: #dc2626;">
                Discount <?php if($bill->discount_percentage > 0): ?>(<?php echo e($bill->discount_percentage); ?>%)<?php endif; ?>:
            </td>
            <td style="text-align: right; color: #dc2626;">
                -<?php echo e(number_format($bill->discount_amount, 2)); ?>

            </td>
        </tr>
    <?php endif; ?>
    
    <?php
        $totalQuantity = $bill->order->orderItems->sum('quantity');
    ?>
    <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 11px;">
        <td style="padding: 3px 0; text-align: left;">
            TOTAL (<?php echo e($totalQuantity); ?> items)
        </td>
        <td style="padding: 3px 0; text-align: right;">
            ₹<?php echo e(number_format($newTotal, 2)); ?>

        </td>
    </tr>
</table>

<!-- Payment Info -->
<div style="margin: 10px 0; padding: 5px 0; border-top: 1px dashed #999;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
        <span>Payment Method:</span>
        <span style="font-weight: 600;"><?php echo e(strtoupper($bill->payment_method ?? 'CASH')); ?></span>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
        <span>Amount Paid:</span>
        <span style="font-weight: 600;"><?php echo e($bill->status === 'paid' ? number_format($bill->total_amount, 2) : '0.00'); ?></span>
    </div>
    <?php if($bill->paid_at): ?>
        <div style="text-align: right; font-size: 8px; color: #666; margin-top: 2px;">
            <?php echo e($bill->paid_at->format('d/m/Y h:i A')); ?>

        </div>
    <?php endif; ?>
</div>

<div class="footer">
    <div>Thank you for dining with us!</div>
    <div>Visit Again</div>
</div>
<?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/bills/partials/receipt.blade.php ENDPATH**/ ?>