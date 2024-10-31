<?php
require_once 'helpers/phpqrcode.php';

class ColorWP_QR_Widget extends WP_Widget {

    function ColorWP_QR_Widget() {
        /* Widget settings */
        $widget_ops = array('classname' => 'colorwp_qr_widget', 'description' => 'Displays QR codes in the sidebar. Configurable options.');
        
        /* Create the widget. */
        $this->WP_Widget('colorwp_qr_widget', 'Qr Code Adv', $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        global $wpdb;
        extract($args);
        
        $title = apply_filters('widget_title', $instance['title']);
        $display_url = ($instance['display_url'])?$instance['display_url']:'current';
        $rawdata = ($instance['rawdata'])?$instance['rawdata']:'';
        $qr_size = $instance['qr_size'];
        
        $qr_background = ($instance['qr_code_bg'] == '' ? '#ffffff' : ''.$instance['qr_code_bg']); /* Default #ffffff */
        $qr_foreground = ($instance['qr_code_fg'] == '' ? '#000000' : ''.$instance['qr_code_fg']); /* Default #000000 */
        
        $qr_code_format = ( $instance['qr_img_format'] == 'jpg' ? 'jpg' : 'png'); // Image format
        $qr_transparency = ($instance['qr_transparent'] == TRUE ? 1 : 0 );
        
        switch($display_url){
            case 'home': // Use always the Wordpress blog homepage
                $data = get_settings('home');
                break;
            case 'rawdata': // Use entered raw text
                $data = $rawdata;
                break;
            case 'current':
            default: // Use the currently viewed page URL
                $data = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    break;
        }
        
        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;

        echo '<img src="';
        echo create_image($data, 'M', $qr_code_format, (int)$qr_size, $qr_transparency, $qr_background, $qr_foreground);
        echo '" alt="qr code">'; ?></br><?php
        echo "&nbsp";
       // echo "<p> <font size=3>One line simple string in blue color</font> </p>";  
        echo '<a href="http://qr-adv.com">Qr code</a>';
        echo " Advertising";
    }

    function form($instance) {
        $defaults = array(
            'title'=>'',
            'display_url'=>'current',
            'qr_size'=>5,
            'rawdata'=>'',
            'qr_img_format'=>'png',
            'qr_transparent'=>'1',
            'qr_code_bg'=>'ffffff',
            'qr_code_fg'=>'000000',
        ); // In first save of the widget, these won't be set so the default values will be loaded
        $instance = wp_parse_args((array) $instance, $defaults);
        
        $title          = $instance['title'];
        $display_url    = $instance['display_url'];
        $qr_size        = $instance['qr_size'];
        $rawdata        = $instance['rawdata'];
        $img_format     = $instance['qr_img_format'];
        $qr_transparent = (bool)$instance['qr_transparent'];
        $qr_background  = $instance['qr_code_bg'];
        $qr_foreground  = $instance['qr_code_fg'];
        
        ?>

        <script type="text/javascript" language="javascript">
            jQuery.noConflict();
            jQuery(document).ready(function($) {
                $('#<?php echo $this->get_field_id('qr_code_bg'); ?>').ColorPicker({
                    onSubmit: function(hsb, hex, rgb, el) {
                        $(el).val(hex);
                        $(el).ColorPickerHide();
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                    },
                    onChange: function(hsb, hex, rgb, el){
                        $('#<?php echo $this->get_field_id('qr_code_bg'); ?>').css('backgroundColor', '#'+hex).attr('value', hex);
                    }
                })
                .bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });
                
                $('#<?php echo $this->get_field_id('qr_code_fg'); ?>').ColorPicker({
                    onSubmit: function(hsb, hex, rgb, el) {
                        $(el).val(hex);
                        $(el).ColorPickerHide();
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                    },
                    onChange: function(hsb, hex, rgb, el){
                        $('#<?php echo $this->get_field_id('qr_code_fg'); ?>').css('backgroundColor', '#'+hex).attr('value', hex);
                    }
                })
                .bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });
            });
        </script>
        
        <p>
            <label>Title: </label>
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('title');?>" value="<? echo attribute_escape($title); ?>">
        </p>
        <p>
            <label>QR contents:</label>
            <select onchange="
                if(jQuery(this).val()=='rawdata')
                    jQuery(this).parent().parent().find('.rawdata').show();
                else
                    jQuery(this).parent().parent().find('.rawdata').hide();
                    " class="widefat" name="<?php echo $this->get_field_name('display_url');?>">
                <option value="home" <?=($display_url=='home')?'selected':'';?>>Site URL</option>
                <option value="current" <?=($display_url=='current')?'selected':'';?>>Current Page</option>
                <option value="rawdata" <?=($display_url=='rawdata')?'selected':'';?>>Raw Data</option>
            </select>
        </p>
        <p class="rawdata" <?=($display_url!='rawdata')?'style="display:none"':'';?>>
            <label>Raw Data:</label>
            <textarea class="widefat" name="<?php echo $this->get_field_name('rawdata');?>"><?=($rawdata!='')?$rawdata:'';?></textarea>
        </p>
        <p>
            <label>Image Format:</label>
            <select name="<?php echo $this->get_field_name('qr_img_format');?>">
                <option value="jpg" <?=($img_format=='jpg')?'selected':'';?>>JPG</option>
                <option value="png" <?=($img_format=='png' || $img_format=='')?'selected':'';?>>PNG</option>
            </select>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('qr_transparent');?>" value="1" <?=($qr_transparent)?'checked':''?>> Transparency (png only)
        </p>
        <p>
            <label>Background Color:</label>
            #<input size="6" type="text" style="background:<?=($qr_background)?'#'.$qr_background:'';?>" value="<?=($qr_background)?$qr_background:'';?>" id="<?php echo $this->get_field_id('qr_code_bg'); ?>" name="<?php echo $this->get_field_name('qr_code_bg');?>">
        </p>
        <p>
            <label>Foreground Color:</label>
            #<input size="6" type="text" style="background:<?=($qr_foreground)?'#'.$qr_foreground:'';?>" value="<?=($qr_foreground)?$qr_foreground:'';?>" id="<?php echo $this->get_field_id('qr_code_fg'); ?>" name="<?php echo $this->get_field_name('qr_code_fg');?>">
        </p>
        <p>
            <label>Widget Size:</label>
            <select id="<?php echo $this->get_field_name('qr_size');?>" name="<?php echo $this->get_field_name('qr_size');?>">
                <option value="3" <?=($qr_size=='3')?'selected':'';?>>3</option>
                <option value="5" <?=($qr_size=='5')?'selected':'';?>>5</option>
                <option value="8" <?=($qr_size=='8')?'selected':'';?>>8</option>
            </select>
        </p>
        <?php
        
        if(qr_debug==TRUE){
            echo '<p><b>Debug information:</b><br>';
            foreach($instance as $key=>$val){
                echo $key . ': ' . $val . '<br>';
            }
        }
    }

    function update($new_instance, $old_instance) {
        // Save widget options
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['display_url'] = $new_instance['display_url'];
        $instance['rawdata'] = $new_instance['rawdata'];
        $instance['qr_size'] = $new_instance['qr_size'];
        $instance['qr_img_format'] = $new_instance['qr_img_format'];
        $instance['qr_transparent'] = (bool)$new_instance['qr_transparent'];
        $instance['qr_code_bg'] = $new_instance['qr_code_bg'];
        $instance['qr_code_fg'] = $new_instance['qr_code_fg'];
        return $instance;
    }

}

function make_qr($text, $file_ident, $format="png", $bg_color="#ffffff", $fg_color="#000000", $transparen_bg=0, $level=QR_ECLEVEL_M, $size=2, $margin=4, $quality=85) {
    $enc = QRencode::factory($level, $size, $margin);
    $data = $enc->encode($text);
    $maxSize = (int) (QR_PNG_MAXIMUM_SIZE / (count($data) + 2 * $margin));
    $img = QRimage::image($data, min(max(1, $size), $maxSize), 4);

    $fg_index = imagecolorclosest($img, 0, 0, 0);
    $bg_index = imagecolorclosest($img, 255, 255, 255);

    $bg = colorConversion($bg_color);
    $fg = colorConversion($fg_color);

    imagecolorset($img, $bg_index, $bg[0], $bg[1], $bg[2]);
    imagecolorset($img, $fg_index, $fg[0], $fg[1], $fg[2]);

    if ($format == 'jpg') {
        if ($file_ident)
            imagejpeg($img, $file_ident . "." . $format, $quality);
        else
            imagejpeg($img, false, $quality);
    }
    elseif ($transparen_bg == 1 && $format == "png") {
        if ($quality > 9)
            $quality = 0;
        imagecolortransparent($img, $bg_index);
        if ($file_ident)
            imagepng($img, $file_ident . "." . $format, $quality);
        else
            imagepng($img, false, $quality);
    }else {
        if ($quality > 9)
            $quality = 1;
        if ($file_ident)
            imagepng($img, $file_ident . "." . $format, $quality);
        else
            imagepng($img, false, $quality);
    }
    imagedestroy($img);
}

function create_image($data, $qr_code_ecc="1", $qr_code_format="png", $qr_code_size="3", $qr_code_trans_bg="0", $qr_code_bg="ffffff", $qr_code_fg="000000") {
    ob_start();
    make_qr($data, false, $qr_code_format, $qr_code_bg, $qr_code_fg, $qr_code_trans_bg, $qr_code_ecc, $qr_code_size);
    $image_data = ob_get_contents();
    ob_end_clean();
    
    $rawimg = "data:image/" . ($qr_code_format == 'jpg' ? 'jpeg' : 'png') . ";base64," . chunk_split(base64_encode($image_data));
 
    return $rawimg;
}

function colorConversion($color) {
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }
    if (strlen($color) == 6) {
        list($r, $g, $b) = array($color[0] . $color[1],
        $color[2] . $color[3],
        $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    $rgb = "$r,$g,$b";
    return array($r, $g, $b);
}

// Register the widget so it's visible in the admin dashboard
function widget_ColorWP_qr_init() {
    register_widget('ColorWP_QR_Widget');
}
?>