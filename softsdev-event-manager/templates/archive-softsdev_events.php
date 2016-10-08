<?php
get_header();
?>
<script>

        jQuery(document).ready(function () {

            jQuery('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: '2015-02-12',
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: [
                    {
                        title: 'All Day Event',
                        start: '2015-02-01'
                    },
                    {
                        title: 'Long Event',
                        start: '2015-02-07',
                        end: '2015-02-10'
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: '2015-02-09T16:00:00'
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: '2015-02-16T16:00:00'
                    },
                    {
                        title: 'Conference',
                        start: '2015-02-11',
                        end: '2015-02-13'
                    },
                    {
                        title: 'Meeting',
                        start: '2015-02-12T10:30:00',
                        end: '2015-02-12T12:30:00'
                    },
                    {
                        title: 'Lunch',
                        start: '2015-02-12T12:00:00'
                    },
                    {
                        title: 'Meeting',
                        start: '2015-02-12T14:30:00'
                    },
                    {
                        title: 'Happy Hour',
                        start: '2015-02-12T17:30:00'
                    },
                    {
                        title: 'Dinner',
                        start: '2015-02-12T20:00:00'
                    },
                    {
                        title: 'Birthday Party',
                        start: '2015-02-13T07:00:00'
                    },
                    {
                        title: 'Click for Google',
                        url: 'http://google.com/',
                        start: '2015-02-28'
                    }
                ]
            });

        });

</script>
<style>

    body {
        margin: 40px 10px;
        padding: 0;
        font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
        font-size: 14px;
    }

    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }

</style>
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
                <div class="sd-content">
                    <?php
                    $count = 0;
                    $break_after = 3;
                    while (have_posts()) {
                            $count++;
                            the_post();
                            global $post;
                            ?>
                            <article id="post-<?php the_ID(); ?>" class="sdff-33 pull-left">
                                <header class="entry-header pd-10">

                                    <h1 class="entry-title" style="display: inline;">			
                                        <?php
                                        //If it has one, display the thumbnail
                                        the_post_thumbnail('thumbnail', array('style' => 'float:left;margin-right:20px;'));
                                        ?>
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h1>

                                    <div class="event-entry-meta">
                                        <div class="sdff-30 pull-left">
                                            <?php
                                            $general_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'general');
                                            $location_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'location');
                                            $timing_settings = $sdem_post_type->softsdev_event_meta_data($post->ID, 'timing');
                                            $gallary_settings = explode(':', $sdem_post_type->softsdev_event_meta_data($post->ID, 'gallery'));
                                            extract($general_settings);
                                            extract($timing_settings);
                                            extract($gallary_settings);
                                            extract($location_settings);
                                            $address = trim(implode("\n", array_filter(array($title, $address_1 . ', ' . $address_2, $city, $state, $zip, $country))));


                                            echo isset($gallary_settings[0]) && wp_get_attachment_image($gallary_settings[0]) ? wp_get_attachment_image($gallary_settings[0]) : CHelper::missingImage();
                                            ?>
                                        </div>
                                        <div class="sdff-60 pull-left mr-lt-5">
                                            <div class="sdff-100">
                                                <div class="sdff-100 pull-left side-heading">Organized By</div>
                                                <div class="sdff-100 pull-left mr-lt-5"><?php echo $organized_by; ?></div>                                            
                                                <div class="sdff-100 pull-left side-heading">Date & Timing</div>
                                                <div class="sdff-100 pull-left mr-lt-5"><?php echo $start_date . ' to ' . $end_date; ?></div>
                                                <div class="sdff-100 pull-left side-heading">Location</div>
                                                <div class="sdff-100 pull-left mr-lt-5"><?php echo $address ? nl2br($address) : 'N/A'; ?></div>
                                            </div>
                                        </div>                                    

                                    </div><!-- .event-entry-meta -->			

                                    <div style="clear:both;"><a href="<?php the_permalink(); ?>">Read More</a></div>
                                </header><!-- .entry-header -->

                            </article><!-- #post-<?php the_ID(); ?> -->


                            <?php
                            if ($count == $break_after) {
                                    $count = 0;
                                    echo '<div style="clear:both;"></div>';
                            }
                    }
                    ?><!--The Loop ends-->
                </div>
                <div style="clear:both;" class="box"></div>
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
<div id='calendar'></div>

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
