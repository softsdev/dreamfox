<div class="sd-content">
    <div class="sdff-100 form-field">
        <label for="name">
            <?php echo __('Price Per Ticket', 'softsdev'); ?>
            <?php echo CHelper::helpIcon(__('Select price per Ticket.', 'softsdev')); ?> 
        </label> 
        <input type="text" name="sdem[registration][price_per_ticket]" value="<?php echo $price_per_ticket; ?>" placeholder="Price/Ticket" />
    </div>
    <div class="sdff-100 form-field">
        <label for="name">
            <?php echo __('Discount per Ticket', 'softsdev'); ?>
            <?php echo CHelper::helpIcon(__('Select discount per Ticket.', 'softsdev')); ?> 
        </label> 
        <input type="text" name="sdem[registration][discount_on_ticket]" value="<?php echo $discount_on_ticket; ?>" placeholder="Discount/Ticket (%)"  />
    </div> 
    <div class="sdff-100 form-field">
        <label>
            <?php echo __('Coupon Code', 'softsdev'); ?>
            <?php echo CHelper::helpIcon(__('Select coupon code and discount over that.', 'softsdev')); ?> 
        </label> 
        <input type="text" name="sdem[registration][coupon_code]" value="<?php echo $coupon_code; ?>" placeholder="Coupon Code" class="sdff-20" />
        <input type="text" name="sdem[registration][coupon_discount]" value="<?php echo $coupon_discount; ?>" placeholder="Coupon Discount (%)" class="sdff-40"  />
    </div> 
</div>