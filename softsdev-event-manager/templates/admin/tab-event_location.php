<div class="sd-content">
    <div class="sdff-100 form-field">                 
        <div class="sdff-40 form-field pull-left box">
            <div class="sdff-100 form-field box">      
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_title">
                        <?php echo __('Title', 'softsdev'); ?>
                        <?php echo CHelper::helpIcon(__('Location Heading.', 'softsdev')); ?>                         
                    </label> 
                    <input type="text" maxlength="30" id="loc_title" name="sdem[location][title]" value="<?php echo $title; ?>" class="sdff-70" placeholder="Title" />
                </div>
                <div class="sdff-100 form-field">

                    <label class="sdff-25" for="loc_address_1">
                        <?php echo __('Address 1', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="50" id="loc_address_1" name="sdem[location][address_1]" value="<?php echo $address_1; ?>" class="sdff-70" placeholder="Address 1" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_address_2">
                        <?php echo __('Address 2', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="50" id="loc_address_2" name="sdem[location][address_2]" value="<?php echo $address_2; ?>" class="sdff-70" placeholder="Address 2" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_city">
                        <?php echo __('City', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="20" id="loc_city" name="sdem[location][city]" value="<?php echo $city; ?>" class="sdff-70" placeholder="City" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_state">
                        <?php echo __('State', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="20" id="loc_state" name="sdem[location][state]" value="<?php echo $state; ?>" class="sdff-70" placeholder="State" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_zip">
                        <?php echo __('Zip', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="10" id="loc_zip" name="sdem[location][zip]" value="<?php echo $zip; ?>" class="sdff-70" placeholder="zip" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="loc_country">
                        <?php echo __('Country', 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="20" id="loc_country" name="sdem[location][country]" value="<?php echo $country; ?>" class="sdff-70" placeholder="Country" />
                </div>
                <div class="sdff-100 form-field">
                    <input id="show_map" class="button" type="button" value="search on map" class="sdff-20 pull-right"/> 
                    <?php echo CHelper::helpIcon(__('Click on "Search on map" to get map.', 'softsdev')); ?>
                </div>
            </div>
            <div class="sdff-100 form-field">
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="contact_number">
                        <?php echo __('Mobile', 'softsdev'); ?>
                        <?php echo CHelper::helpIcon(__('Select Contact Number to contact over mobile for this event.'), 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="15" name="sdem[location][contact_number]" value="<?php echo $contact_number; ?>" class="sdff-70" placeholder="Contact Number" />
                </div>
                <div class="sdff-100 form-field">
                    <label class="sdff-25" for="contact_email">
                        <?php echo __('Email', 'softsdev'); ?>
                        <?php echo CHelper::helpIcon(__('Select Contact email to contact over emails for this event.'), 'softsdev'); ?>
                    </label> 
                    <input type="text" maxlength="40" name="sdem[location][contact_email]" value="<?php echo $contact_email; ?>" class="sdff-70" placeholder="Contact Email" />  
                </div>
            </div>
        </div>
        <div class="sdff-60 form-field pull-left">
            <div class="sdff-100 form-field box">
                <textarea id="full_address" rows="6" disabled="disabled"></textarea>          
            </div>
            <div id="event_location_map" class="location_map"><img src="<?php echo esc_url(SD_EVENTS_IMAGES . '/missing_map.png'); ?>"></div>
        </div>         
    </div>    
</div>