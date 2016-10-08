<?php
get_header();
?>

<div id="primary" class="site-content">
    <div id="content" role="main">

        <!-- Page header-->
        <header class="page-header">
            <h1 class="page-title"><?php echo __('Events: ', 'softsdev'); ?></h1>
        </header>

        <?php if (have_posts()) { ?>

                <?php
                global $wp_query;
                if ($wp_query->max_num_pages > 1) {
                        ?>
                        <nav id="nav-above">
                            <div class="nav-next events-nav-newer"><?php next_posts_link(__('Later events <span class="meta-nav">&rarr;</span>', 'softsdev')); ?></div>
                            <div class="nav-previous events-nav-newer"><?php previous_posts_link(__(' <span class="meta-nav">&larr;</span> Newer events', 'softsdev')); ?></div>
                        </nav><!-- #nav-above -->
                <?php } ?>

                <?php /* Start the Loop */ ?>

                <?php
                while (have_posts()) {
                        the_post();
                        global $post;
                        ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <header class="entry-header sd-content">

                                <h1 class="entry-title" style="display: inline;">			
                                    <?php
                                    //If it has one, display the thumbnail
                                    the_post_thumbnail('thumbnail', array('style' => 'float:left;margin-right:20px;'));
                                    ?>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h1>

                                <div class="event-entry-meta">
                                    <?php
                                    $general_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'general');
                                    $registration_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'registration');
                                    $location_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'location');
                                    $timing_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'timing');
                                    $gallery_images = explode(':', $sdem_post_type->softsdev_event_meta_data($post->ID, 'gallery'));
                                    extract($general_settings);
                                    extract($registration_settings);
                                    extract($timing_settings);
                                    extract($location_settings);
                                    $address = trim(implode("\n", array_filter(array($title, $address_1 . ', ' . $address_2, $city, $state, $zip, $country))));
                                    ?>
                                    <div class="sdff-100">
                                        <div class="sdff-100">
                                            <div class="sdff-30 pull-left">Organized By</div>
                                            <div class="sdff-70 pull-left"><?php echo $organized_by; ?></div>      
                                            <div class="sdff-30 pull-left">Special Guest</div>
                                            <div class="sdff-70 pull-left"><?php echo $special_guest; ?></div>                                              
                                            <div class="sdff-30 pull-left">Date & Timing</div>
                                            <div class="sdff-70 pull-left"><?php echo $start_date . ' to ' . $end_date; ?></div>
                                            <div class="sdff-30 pull-left">Registration dates</div>
                                            <div class="sdff-70 pull-left"><?php echo $registration_opening_date . ' to ' . $cut_off_date; ?></div> 
                                            <div class="sdff-30 pull-left">Ticket</div>
                                            <div class="sdff-70 pull-left">Only Rs <?php echo $price_per_ticket; ?>/ticket</div>                                             
                                        </div>
                                    </div> 
                                    <div class="sdff-100 clear-both">
                                        <?php the_content(); ?>
                                    </div>  
                                    <div class="sdff-100 clear-both">
                                        <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                            <input type="hidden" name="cmd" value="_xclick">
                                            <input type="hidden" name="business" value="mail.softsdev@gmail.com">
                                            <input name="return" type="hidden" value="http://www.google.com/" /> 
                                            <input type="hidden" name="currency_code" value="USD">
                                            <input type="hidden" name="item_name" value="Event">
                                            <input type="hidden" name="amount" value="100.00">
                                            <div class="sdff-100">
                                                <div class="sdff-30 pull-left">Size</div>
                                                <div class="sdff-70 pull-left">
                                                    <input type="hidden" name="on0" value="Size">
                                                    <select name="os0">
                                                        <option value="Select a Size">Select a Size
                                                        <option value="Small">Small
                                                        <option value="Medium">Medium
                                                        <option value="Large">Large
                                                    </select>
                                                </div>                                                                                                                                          </div>
                                            <div class="sdff-100">
                                                <div class="sdff-30 pull-left">Color</div>
                                                <div class="sdff-70 pull-left">
                                                    <input type="hidden" name="on1" value="Color">
                                                    <input type="text" name="os1" maxlength="200">
                                                </div>                                                                                                                                          </div> 
                                            <div class="sdff-100">

                                                <input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                                            </div> 

                                        </form>  
                                    </div>
                                    <div class="sdff-100 clear-both">
                                        <div class="sdff-40 pull-left">
                                            <div class="sdff-100 pull-left">Location</div>
                                            <div class="sdff-100 pull-left"><?php echo nl2br($address); ?></div>      
                                        </div>
                                        <div class="sdff-60 pull-left">
                                            MAP HERE
                                            <?php //echo $sdem_obj->getGoogleMap(trim(implode("+", array_filter(array($address_1, $address_2, $city, $state, $zip, $country))))); ?>
                                        </div>
                                    </div>     
                                    <div class="sdem_gallery sdff-100 clear-both">
                                        <h3>Images</h3>
                                        <?php foreach ($gallery_images as $image_id) { ?>
                                                <dl class="sdem_gallery-item">
                                                    <dt class="gallery-icon">
                                                    <a rel="lightbox" href="<?php echo wp_get_attachment_url($image_id); ?>" data-lightbox="sdem_image_set"><?php echo wp_get_attachment_image($image_id); ?></a>                                                    
                                                    </dt>
                                                </dl>
                                        <?php } ?>
                                    </div>
                                    <!-- .event-entry-meta -->			
                            </header><!-- .entry-header -->

                        </article><!-- #post-<?php the_ID(); ?> -->


                <?php } ?><!--The Loop ends-->

                <!-- Navigate between pages-->
                <?php if ($wp_query->max_num_pages > 1) { ?>
                        <nav id="nav-below">
                            <div class="nav-next events-nav-newer"><?php next_posts_link(__('Later events <span class="meta-nav">&rarr;</span>', 'softsdev')); ?></div>
                            <div class="nav-previous events-nav-newer"><?php previous_posts_link(__(' <span class="meta-nav">&larr;</span> Newer events', 'softsdev')); ?></div>
                        </nav><!-- #nav-below -->
                <?php } ?>

        <?php } else { ?>
                <!-- If there are no events -->
                <article id="post-0" class="post no-results not-found">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php _e('Nothing Found', 'softsdev'); ?></h1>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <p><?php _e('no results were found for Events. ', 'softsdev'); ?></p>
                    </div><!-- .entry-content -->
                </article><!-- #post-0 -->

        <?php } ?>

    </div><!-- #content -->
</div><!-- #primary -->

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
