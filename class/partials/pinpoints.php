<script>
 wemapenv = {
    'api': '<?php echo WEMAP_API_URL;?>',
    'livemap': '<?php echo WEMAP_LIVEMAP_URL;?>',
    'user': '<?php echo $_SESSION['user']->id;?>'
};
</script>
<div class="inside">
    <div class="pinpoint-display" id="pinpoint_display">
        <p>
        To create a smart map for this post, choose ‘image thumbnail’ and upload an image or choose icon and select an icon then enter the location in the address field.
        </p>
        <iframe width="400" height="300" id="preview_pp" src=""></iframe>
        <label>
            <span class="label">Type :</span>
            <select name="list-pinpoints" id="list-pinpoints">
                <option value="2">image thumbnail</option>
                <option value="1">icon</option>
            </select>
        </label>
        <div id="catpoint_pick">
            <div id="dialog" title="Category">
            </div>
            <input type="hidden" name="id_cat" src="" id="id_cat" value="1" />
            <img src="<?php echo Admin_Wemap_Meta_Box::wemap_path_plugin('images/icon_circle_maaap.png');?>" style="height:39px;" id="opener"/>
        </div>
        <div>
            <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>
            <div id="picture_pp">
                <label>
                    <span class="label">Picture :</span>
                    <input type="file" name="image_picpoint" id="image_picpoint" placeholder="url" onkeydown="if (event.keyCode == 13) return false;">
                </label>
            </div>
            <label>
                <span class="label">Address :</span>
                <input id="autocomplete" placeholder="Enter your address" onFocus="wemap_geolocate();" type="text" onkeydown="if (event.keyCode == 13) return false;">
                <select name="list_search" id="list_search">
                    <option value=""></option>
                </select><br>
            </label>
            <label>
                <span class="label">Latitude :</span>
                <input type="text" name="pinpoint_latitude" id="pinpoint_latitude" onkeydown="if (event.keyCode == 13) return false;"><br>
            </label>
            <label>
                <span class="label">Longitude :</span>
                <input type="text" name="pinpoint_longitude" id="pinpoint_longitude" onkeydown="if (event.keyCode == 13) return false;"><br>
            </label>
            <label>
            <label>
                <span class="label">Insert into :</span>
                <select name="choice-insert" id="choice-insert">
                    <option value="" select="selected"></option>
                    <option value="lists">Lists</option>
                </select>
            </label>
            <br>
            <div id="choice-insert-lists">
                <select name="list-lists-pinpoint" id="list-lists-pinpoint">
                </select>
            </div>
            <br>
            <input type="hidden" name="new_pinpoint_id" id="new_pinpoint_id" />
            <button id="btn_wemap_save" type="button">Save</button>
        </div>
    </div>
</div>
