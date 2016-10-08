<?php
$week_day = isset($week_day) ? $week_day : array();
?>
<div class="sd-content">
    <div class="sdff-100 form-field box">
        <div class="sdff-100 form-field pull-left">
            <label for="start_date">
                <?php echo __('Start/End DateTime', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Select Event Start/End DateTime', 'softsdev')); ?>                    
            </label> 
            <input type="text" value="<?php echo $start_date; ?>" id="date_timepicker_start" name="sdem[timing][start_date]" placeholder="Start Date" class="sdff-25" >  
            <input type="text" value="<?php echo $end_date; ?>" id="date_timepicker_end" name="sdem[timing][end_date]" placeholder="End Date" class="sdff-25" >  
        </div> 
    </div>  
    <div class="sdff-100 form-field box">
        <div class="sdff-100 form-field weeks_block">
            <label for="start_date" class="pull-left">
                <?php echo __('On', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Select Week Days to excute weekly Event', 'softsdev')); ?>                    
            </label>         
            <div class="sdff-10 form-field pull-left mr-lt-5"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('mon', $week_day)); ?> value="mon">Mon</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('tue', $week_day)); ?> value="tue">Tue</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('wed', $week_day)); ?> value="wed">Wed</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('thu', $week_day)); ?> value="thu">Thu</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('fri', $week_day)); ?> value="fri">Fri</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('sat', $week_day)); ?> value="sat">Sat</div>          
            <div class="sdff-10 form-field pull-left"><input type="checkbox" name="sdem[timing][week_day][]" <?php checked(in_array('sun', $week_day)); ?> value="sun">Sun</div>          
        </div>    
        <div class="sdff-100 form-field till_date_block">
            <label for="till_date">
                <?php echo __('Till Date', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Select until when this event is occuring.', 'softsdev')); ?>                    
            </label> 
            <input type="text" value="<?php echo $till_date; ?>" id="till_date" datatimepicker="true" name="sdem[timing][till_date]" class="sdff-25" placeholder="Till Date" >  
        </div>  
    </div>
    <div class="sdff-100 form-field box registration_date_block">
        <div class="sdff-100 form-field pull-left">
            <label for="registration_opening_date">
                <?php echo __('Registration Opening', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Select Registration date limit', 'softsdev')); ?>                    
            </label> 
            <input type="text" value="<?php echo $registration_opening_date; ?>" id="registration_opening_date" name="sdem[timing][registration_opening_date]" placeholder="Registration Opening Date" class="sdff-25"  >  
            <input type="text" value="<?php echo $cut_off_date; ?>" id="cut_off_date" name="sdem[timing][cut_off_date]" placeholder="Cut Off Date" class="sdff-25"  >  
        </div> 
    </div>     
</div>