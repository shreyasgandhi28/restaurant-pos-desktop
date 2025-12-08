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
    <?php endif; ?>
</div>

<div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

<!-- Bill Info -->
<div class="bill-info" style="margin: 3px 0;">
    <div>Table: <?php echo e($bill->order->restaurantTable->table_number); ?></div>
</div>
<div class="bill-info" style="margin: 3px 0;">
    <div>Bill No: <?php echo e($bill->bill_number); ?></div>
</div>
<div class="bill-info" style="margin: 3px 0;">
    <div>Date: <?php echo e($bill->created_at->format('d/m/Y h:i A')); ?></div>
</div>
<div class="bill-info" style="margin: 3px 0 5px;">
    <div>Status: <span style="text-transform: uppercase;"><?php echo e($bill->status); ?></span></div>
</div>

<div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

<!-- Items Table -->
<table class="items-table">
    <thead>
        <tr>
            <th style="text-align: left; width: 45%;">ITEM</th>
            <th style="text-align: center; width: 10%;">QTY</th>
            <th style="text-align: right; width: 20%;">RATE</th>
            <th style="text-align: right; width: 25%;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="text-align: left; padding: 4px 0;">
                    <?php echo e($item->menuItem->name); ?>

                    <?php if($item->notes): ?>
                        <div style="font-size: 9px; color: #000; font-style: italic; font-weight: bold;">
                            <?php echo e($item->notes); ?>

                        </div>
                    <?php endif; ?>
                </td>
                <td style="text-align: center; padding: 4px 0;"><?php echo e($item->quantity); ?></td>
                <td style="text-align: right; padding: 4px 0;"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                <td style="text-align: right; padding: 4px 0;"><?php echo e(number_format($item->total_price, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<div style="border-top: 1px dashed #000; margin: 5px 0;"></div>

<!-- Totals -->
<table class="totals-table" style="width: 100%; margin: 5px 0;">
    <tr>
        <td style="text-align: left; padding: 2px 0;">Sub Total:</td>
        <td style="text-align: right; padding: 2px 0;">₹<?php echo e(number_format($bill->subtotal, 2)); ?></td>
    </tr>
    
    <?php if($taxAmount > 0): ?>
        <tr>
            <td style="text-align: left; padding: 2px 0;">CGST (<?php echo e(number_format($taxRate / 2, 1)); ?>%):</td>
            <td style="text-align: right; padding: 2px 0;">₹<?php echo e(number_format($taxAmount / 2, 2)); ?></td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 2px 0;">SGST (<?php echo e(number_format($taxRate / 2, 1)); ?>%):</td>
            <td style="text-align: right; padding: 2px 0;">₹<?php echo e(number_format($taxAmount / 2, 2)); ?></td>
        </tr>
    <?php endif; ?>
    
    <?php if($serviceCharge > 0): ?>
        <tr>
            <td style="text-align: left; padding: 2px 0;">Service Charge (<?php echo e(number_format($serviceChargeRate, 2)); ?>%):</td>
            <td style="text-align: right; padding: 2px 0;">₹<?php echo e(number_format($serviceCharge, 2)); ?></td>
        </tr>
    <?php endif; ?>
    
    <?php if($bill->discount_amount > 0): ?>
        <tr>
            <td style="text-align: left; padding: 2px 0;">Discount (<?php echo e(number_format($bill->discount_percentage, 2)); ?>%):</td>
            <td style="text-align: right; padding: 2px 0;">-₹<?php echo e(number_format($bill->discount_amount, 2)); ?></td>
        </tr>
    <?php endif; ?>
    
    <?php
        $totalQuantity = $items->sum('quantity');
    ?>
    <tr class="grand-total">
        <td style="padding: 4px 0; text-align: left;">
            TOTAL (<?php echo e($totalQuantity); ?> items)
        </td>
        <td style="padding: 4px 0; text-align: right;">
            ₹<?php echo e(number_format($newTotal, 2)); ?>

        </td>
    </tr>
</table>

<!-- Payment Info -->
<div class="payment-section" style="margin: 5px 0;">
    <div style="margin: 3px 0;">
        <span>Payment Method:</span>
        <span style="font-weight: bold; margin-left: 5px;"><?php echo e(strtoupper($bill->payment_method ?? 'CASH')); ?></span>
    </div>
    <?php if($bill->paid_at): ?>
        <div style="margin: 3px 0;">
            <span>Paid At:</span>
            <span style="margin-left: 5px;"><?php echo e($bill->paid_at->format('d/m/Y h:i A')); ?></span>
        </div>
    <?php endif; ?>
</div>

<div class="footer">
    <div>Thank you for dining with us!</div>
    <div>Visit Again</div>
</div>
<?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/bills/partials/receipt.blade.php ENDPATH**/ ?>