/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function ($) {

    /**
     * Enabling tab with softsdev-tabs class
     */
    jQuery(".softsdev-tabs, .softsdev-vertical-tabs").tabs();
    jQuery(".softsdev-vertical-tabs").addClass('ui-helper-clearfix');

    /**
     * Media Gallary Management
     * @type @exp;wp@pro;media@pro;frames@pro;file_framewp@call;media
     */
    var gallery_uploader;


    jQuery('#upload_image_button').click(function (e) {
        var max_images = 20;
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (gallery_uploader) {
            gallery_uploader.open();
            return;
        }

        //Extend the wp.media object
        gallery_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        gallery_uploader.on('select', function () {
            var selection = gallery_uploader.state().get('selection');
            var _data = '';
            var _images = '';
            if (selection.length > max_images) {
                alert("You can only add max " + max_images + " images");
            } else {
                selection.map(function (attachment) {
                    attachment = attachment.toJSON();
                    _data += _data ? (':' + attachment.id) : attachment.id;
                    // Do something with attachment.id and/or attachment.url here
                    _images += '<dl class="sdem_gallery-item"><dt class="gallery-icon"><img style="" src=' + attachment.sizes.thumbnail.url + '></dt></dl>';
                });
                $('.sdem_gallery').html(_images);
                jQuery('#upload_image').val(_data);
            }
        }).on('open', function () {
            var selection = gallery_uploader.state().get('selection');
            ids = jQuery('#upload_image').val().split(':');
            ids.forEach(function (id) {
                attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });
        });
        ;

        //Open the uploader dialog
        gallery_uploader.open();

    });

    /**
     * DateTiem picker for start and end date
     */
    setStartDateEndDateTiemPicker(jQuery('#date_timepicker_start'), jQuery('#date_timepicker_end'));
    setStartDateEndDateTiemPicker(jQuery('#registration_opening_date'), jQuery('#cut_off_date'));

    /**
     * Start Date End date with date selectors
     * @param {type} startdate_selector_id
     * @param {type} enddate_selector_id
     * @returns {undefined}
     */
    function setStartDateEndDateTiemPicker(startdate_selector_id, enddate_selector_id) {

        startdate_selector_id.datetimepicker({
            onShow: function (ct) {
                var parts = enddate_selector_id.val().match(/(\d+)/g);
                var current = ct.format0();
                this.setOptions({
                    maxDate: enddate_selector_id.val() ? parts[0] + '/' + parts[1] + '/' + parts[2] : false,
                    maxTime: enddate_selector_id.val() && (parts[0] + '/' + parts[1] + '/' + parts[2] == current) ? parts[3] + ':' + parts[4] : false
                })
            },
            onSelectDate: function (ct) {
                var parts = enddate_selector_id.val().match(/(\d+)/g);
                var current = ct.format0();
                this.setOptions({
                    maxDate: enddate_selector_id.val() ? parts[0] + '/' + parts[1] + '/' + parts[2] : false,
                    maxTime: enddate_selector_id.val() && (parts[0] + '/' + parts[1] + '/' + parts[2] == current) ? parts[3] + ':' + parts[4] : false
                })
            },
            timepicker: true
        });
        enddate_selector_id.datetimepicker({
            onShow: function (ct) {
                var parts = startdate_selector_id.val().match(/(\d+)/g);
                var current = ct.format0();
                this.setOptions({
                    minDate: startdate_selector_id.val() ? parts[0] + '/' + parts[1] + '/' + parts[2] : false,
                    minTime: startdate_selector_id.val() && (parts[0] + '/' + parts[1] + '/' + parts[2] == current) ? parts[3] + ':' + parts[4] : false
                })
            },
            onSelectDate: function (ct) {
                var parts = startdate_selector_id.val().match(/(\d+)/g);
                var current = ct.format0();
                this.setOptions({
                    minDate: startdate_selector_id.val() ? parts[0] + '/' + parts[1] + '/' + parts[2] : false,
                    minTime: startdate_selector_id.val() && (parts[0] + '/' + parts[1] + '/' + parts[2] == current) ? parts[3] + ':' + parts[4] : false
                })
            },
            timepicker: true
        });
    }
    
    /**
     * Normal Date picker having input attribute (datatimepicker="true")
     */
    jQuery('input[datatimepicker="true"]').datetimepicker({
        onShow: function (ct) {
            this.setOptions({
                minDate: new Date(),
            })
        },
        timepicker: false
    });
});