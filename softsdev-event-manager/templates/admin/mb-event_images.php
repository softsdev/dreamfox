<div class="sd-content">
    <div class="sdff-100 form-field">
        <input id="upload_image" type="hidden" size="36" name="sdem[gallery]" value="<?php echo $gallery; ?>" /> 
        <input id="upload_image_button" class="sdff-20 button" type="button" value="Select Images"  />
    </div>
    <h4>Selected Images</h4>
    <?php $images = explode(':', $gallery) ?>
    <div class="sdem_gallery sdff-100">
        <?php foreach ($images as $image_id) { ?>
                <dl class="sdem_gallery-item">
                    <dt class="gallery-icon">
                    <?php echo wp_get_attachment_image($image_id); ?>
                    </dt>
                </dl>
        <?php } ?>
    </div>
</div>