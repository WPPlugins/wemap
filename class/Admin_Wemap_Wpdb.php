<?php
class Admin_Wemap_Wpdb {
    static public function wemap_add_wpdb($id_post, $id_pp) {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM ". WEMAP_PINPOINT_TABLE ." WHERE id_post = '$id_post' AND id_pinpoint = '$id_pp'");
        if (is_null($row)) {
            $wpdb->insert(WEMAP_PINPOINT_TABLE, array('id_post'=>$id_post, 'id_pinpoint'=>$id_pp));
        }
    }

    static public function wemap_edit_post($id_post) {
        global $wpdb;
        $wpdb_post = $wpdb->get_row("SELECT * FROM ". WEMAP_PINPOINT_TABLE ." WHERE id_post = '$id_post'");

        if (isset($wpdb_post)) {
            $connect_serv = new Connect_To_Serv();
            $id_pp = $wpdb_post->id_pinpoint;
            $edited_pp = json_decode($connect_serv->wemap_get_requet('/v3.0/pinpoints/'.$id_pp));
            return $edited_pp;
        } else {
            return null;
        }
    }
}
?>
