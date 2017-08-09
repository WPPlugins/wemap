<?php
class Publish_Post {
    public function __construct () {
        add_action('save_post', array($this, 'wemap_pinpoints_post'), 10, 2);
        add_action('delete_post', array($this, 'wemap_pinpoints_delete'), 10, 1);
        add_action( 'post_edit_form_tag', array($this, 'post_edit_form_tag') );
    }

    public function post_edit_form_tag( ) {
        echo ' enctype="multipart/form-data"';
    }

    public function wemap_pinpoints_delete($id) {
        global $wpdb;
        $wpdb_post = $wpdb->get_row("SELECT * FROM ". WEMAP_PINPOINT_TABLE ." WHERE id_post = '$id'");
        if (isset($wpdb_post)) {
            $wpdb->delete(WEMAP_PINPOINT_TABLE, array('id'=>$wpdb_post->id, 'id_post'=>$wpdb_post->id_post, 'id_pinpoint'=>$wpdb_post->id_pinpoint));
        }
    }

    public function wemap_pinpoints_post($id, $post_info) {
        if (!isset($_POST['pinpoint_latitude']) && !isset($_POST['pinpoint_longitude']))
            return;

        $connect_serv = new Connect_To_Serv();

        if (! empty($_POST['new_pinpoint_id'])){
            Admin_Wemap_Wpdb::wemap_add_wpdb(intval($id), intval($_POST['new_pinpoint_id']));
            return;
        }       

        $description = $post_info->post_content;
        $description = preg_replace('/\[livemap\s+.+?\]/i', "\n", $description);
        $description = preg_replace('/\[mini_livemap\s+.+?\]/i', "\n", $description);

        $pinpoint = array(
            'description' => $description . '<br>',
            'latitude' => floatval($_POST['pinpoint_latitude']),
            'longitude' => floatval($_POST['pinpoint_longitude']),
            'name' => $post_info->post_title,
            'category' => isset($_POST['id_cat'])? intval($_POST['id_cat']) : 1,
            'tags' => array()
            );

        foreach ( get_the_tags() as $tag ) {
            array_push($pinpoint['tags'], $tag->name);
        }

        if (! empty($_FILES['image_picpoint']['name'])) {
            $pinpoint['media_file'] = $connect_serv->get_media_json($_FILES['image_picpoint']);
        }

        $edited_pp = Admin_Wemap_Wpdb::wemap_edit_post($id);
        if (empty($edited_pp)) {
            $newpinpoint = json_decode($connect_serv->wemap_post_requet('/v3.0/pinpoints', json_encode($pinpoint)));
            if (!isset($newpinpoint))
                return;
            Admin_Wemap_Wpdb::wemap_add_wpdb(intval($id), intval($newpinpoint->id));
        } else {
            $newpinpoint = json_decode($connect_serv->wemap_put_requet('/v3.0/pinpoints/' . $edited_pp->id, json_encode($pinpoint)));
        }
        if ($_POST["choice-insert"] == "lists") {
            $list_id = $_POST['list-lists-pinpoint'];
            $connect_serv->wemap_post_requet('/v3.0/lists/'. $list_id .'/pinpoints', json_encode(
                array(array('id' => intval($newpinpoint->id)))
            ));
        }
    }
}
?>
