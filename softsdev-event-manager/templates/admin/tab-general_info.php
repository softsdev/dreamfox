<div class="sd-content">
    <div class="sdff-100 form-field box">
        <div class="sdff-100 form-field">
            <label for="organized_by">
                <?php echo __('Organized By', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('put organized by name here.', 'softsdev')); ?>
            </label> 
            <input type="text" size="6" name="sdem[general][organized_by]" value="<?php echo $organized_by; ?>" class="sdff-40" placeholder="Organized By" />
        </div>  
        <div class="sdff-100 form-field">
            <label for="people_capacity">
                <?php echo __('People Capacity', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Put capacity of attendies for event at location.', 'softsdev')); ?>
            </label> 
            <input type="text" size="6" name="sdem[general][people_capacity]" value="<?php echo $people_capacity; ?>" class="sdff-10" placeholder="Capacity" />
        </div>      
        <div class="sdff-100 form-field">
            <label for="special_guest">
                <?php echo __('Special Guest', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('mention special guest to attend this event.', 'softsdev')); ?>                
            </label> 
            <textarea  name="sdem[general][special_guest]" rows="5" placeholder="Special Guest" ><?php echo $special_guest; ?></textarea>
        </div> 
    </div>
    <div class="sdff-100 form-field box">
        <label for="event_type">
            <?php echo __('Event Type', 'softsdev'); ?>
            <?php echo CHelper::helpIcon(__('Select Event type here.', 'softsdev')); ?>            
        </label> 
        <select  name="sdem[general][event_type]"  class="sdff-20" >
            <option value="1" <?php selected($event_type, 1); ?>>Custom</option>
            <option value="2" <?php selected($event_type, 2); ?>>Weekly</option>
            <option value="3" <?php selected($event_type, 3); ?>>Daily</option>
            <option value="4" <?php selected($event_type, 4); ?>>Monthly</option>
            <option value="5" <?php selected($event_type, 5); ?>>Yearly</option>
        </select>
    </div>     
    <div class="sdff-100 form-field box">
        <div class="sdff-100 form-field">
            <label for="is_enable_registration">
                <?php echo __('Enable Registration', 'softsdev'); ?>
            </label> 
            <input type="checkbox" name="sdem[general][is_enable_registration]" value="1" <?php checked(@$is_enable_registration); ?>/>
        </div>   
        <div class="sdff-100 form-field">
            <label for="registration_type">
                <?php echo __('Registration Type', 'softsdev'); ?>
                <?php echo CHelper::helpIcon(__('Select Registration type here.', 'softsdev')); ?>                  
            </label> 
            <select  name="sdem[general][registration_type]" class="sdff-20" >
                <option value="1" <?php selected($registration_type, 1); ?>>Custom</option>
                <option value="2" <?php selected($registration_type, 2); ?>>Full Day</option>
            </select>
        </div>    
    </div>
</div>