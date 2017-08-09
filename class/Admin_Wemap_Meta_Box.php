<?php
class Admin_Wemap_Meta_Box {
    public function __construct () {
        add_action('add_meta_boxes', array($this, 'wemap_add_meta_box_pinpoints'));
        if (isset($_COOKIE['token_wemap'])) {
            add_action('media_buttons_context', array($this, 'wemap_livemap_button'));
            add_action('media_buttons_context', array($this, 'wemap_pinpoint_button'));
            add_action('admin_footer', array($this, 'wemap_popup_content'));
        }
    }

    public function wemap_livemap_button($context) {
        $img = plugins_url('images/w_wemap.png', dirname(__FILE__));
        $title = 'Select a Connected Map to insert into post';
        $container_id = 'popup_wemap';
        $context .= "<a class='button thickbox' title='{$title}'
        href='#TB_inline?width=750&inlineId={$container_id}'>
        <span class='wp-media-buttons-icon' style='background: url({$img}); background-repeat: no-repeat; background-position: left bottom;'></span>
        Embed map in your post
        </a>";

        return $context;    
    }

    static public function wemap_path_plugin($path) {
        return plugins_url($path, dirname(__FILE__));
    }

    public function wemap_pinpoint_button($context) {
        $disabled = "disabled='disabled'";
        $title = 'You need to add this post to a Livemap first';
        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            $id_post = get_the_ID();
            $edited_pp = Admin_Wemap_Wpdb::wemap_edit_post($id_post);
            if (!empty($edited_pp)) {
                $title = 'Add a sigle-point map to my post';
                $disabled = '';
                ?>

                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        var type = document.getElementById('list-pinpoints');
                        var latitude = document.getElementById('pinpoint_latitude');
                        var longitude = document.getElementById('pinpoint_longitude');
                        var category = document.getElementById('opener');
                        var id_category = document.getElementById('id_cat');
                        var preview_pp = document.getElementById('preview_pp');

                        type.value = '<?php echo ($edited_pp->media_type); ?>';
                        latitude.value = '<?php echo ($edited_pp->latitude); ?>';
                        longitude.value = '<?php echo ($edited_pp->longitude); ?>';
                        
                        if (type.value === '') {
                            document.getElementById('catpoint_pick').style.display = 'block';
                            id_category.value = '<?php echo ($edited_pp->category); ?>';
                            id_category.src = '<?php echo ($edited_pp->image_url); ?>';
                            category.src = '<?php echo ($edited_pp->image_url); ?>';
                        }

                        jQuery('#button-add-pp').on('click', function() {
                            var id_pp = '<?php echo ($edited_pp->id); ?>';
                            window.send_to_editor("[mini_livemap width='600' height='400' src='" + id_pp + '&token=' + window.wemap_getCookie('token_wemap') + "']");
                        });

                        preview_pp.width = 400;
                        preview_pp.height = 300;
                        preview_pp.src = wemapenv.livemap + 'enabledcontrols=false&ppid=<?php echo ($edited_pp->id); ?>';
                    });
                </script>
                <?php
            }
        }
        $img = plugins_url('images/w_wemap.png', dirname(__FILE__));
        $context .= "<a class='button' id='button-add-pp' title='{$title}' {$disabled} ><span class='wp-media-buttons-icon' style='background: url({$img}); background-repeat: no-repeat; background-position: left bottom; '>
        </span>
        Add smart map to your post
        </a>";
        return $context;
    }

    public function wemap_popup_content() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                var selectsrc = document.getElementById('list-livemaps');
                var livemap_preview = document.getElementById('livemap_preview');
                var height = document.getElementById('height-livemap').value;
                var    width = document.getElementById('width-livemap').value;
                if (selectsrc.options.length > 1) {
                    document.getElementById('any-livemap').style.display = 'none';
                    document.getElementById('several-livemap').style.display = 'block';
                } else {
                    document.getElementById('any-livemap').style.display = 'block';
                    document.getElementById('several-livemap').style.display = 'none';
                }
                jQuery('#button-livemap').on('click', function() {
                    if (selectsrc.value != '') {
                        var height = document.getElementById('height-livemap').value;
                        var    width = document.getElementById('width-livemap').value;
                        window.send_to_editor("[livemap width='" + width + "' height='" + height + "' src='" + window.wemap_getCookie('token_wemap') + '&emmid=' + selectsrc.options[selectsrc.selectedIndex].value + "']");
                    }
                    tb_remove();
                });
                jQuery('#list-livemaps').on('change',function(){
                    if (selectsrc.options[selectsrc.selectedIndex].value == '')
                        livemap_preview.src = '';
                    else
                        livemap_preview.src = wemapenv.livemap + 'token=' + window. wemap_getCookie('token_wemap') + '&emmid=' + selectsrc.options[selectsrc.selectedIndex].value;
                });
                jQuery('#width-livemap').on('change',function(){
                    livemap_preview.width = this.value;
                });
                jQuery('#height-livemap').on('change',function(){
                    livemap_preview.height = this.value;
                });
            });
        </script>
        <div id="popup_wemap" style="display:none;">
            <h2>Add a Connected Map</h2>
            <div id="any-livemap">You haven't created any Livemap yet.<br>
                Connect to <a href="https://pro.getwemap.com/#/app/livemaps/new" target="_blank">Wemap platform</a> to create one before you can add it to your post.
            </div>
            <div id="several-livemap"><a href="https://pro.getwemap.com/#/app/livemaps/new" target="_blank">Create a Connected Map</a></div>
            <select id="list-livemaps">
                <option value="" select="selected"></option>
                <input type="button" id="button-livemap" value="Select">
            </select><br>
            <span>Size : </span><br>
            <span>Width</span>
            <input id="width-livemap" type="number" value="600" min="0"/>
            <span>Height</span>
            <input id="height-livemap" type="number" value="400" min="0"/><br>
            <iframe id ='livemap_preview' width="600" height="400" src="" ></iframe>
        </div>
        <?php    
    }

    public function wemap_add_meta_box_pinpoints() {
        add_meta_box('Wemap_interface_pinpoints', "Create a smart map for this post", array($this, 'wemap_display_pinpoints_meta_box'), 'post', 'normal', 'default');
    }

    public function wemap_display_pinpoints_meta_box() {
        if (isset($_COOKIE['token_wemap'])) {
            include_once('partials/pinpoints.php');
        } else {
            echo '<a href=' . site_url('wp-admin/admin.php?page=wemap_connexion') . '>Please login.</a>';
        }
    }
}
?>
