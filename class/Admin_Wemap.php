<?php
class Admin_Wemap {
    public function __construct($version, $name) {
        Connect_To_Serv::wemap_start_session();
        add_action('admin_menu', array($this, 'wemap_admin_menu'), 20);
        $this->wemap_co();
        new Admin_Wemap_Interface($version, $name);
        new Publish_Post();
    }

    public function wemap_admin_menu() {
        add_menu_page('Wemap Plugin Help', 'Wemap Plugin', 'manage_options', 'wemap', array($this, 'wemap_html'), plugins_url('images/w_wemap.png', dirname(__FILE__)));

        $html_menu = 'wemap_deconnexion_html';
        $name = 'Logout';
        if (!isset($_COOKIE['token_wemap'])) {
            $html_menu = 'wemap_connexion_html';
            $name = 'Log in';
        }
        add_submenu_page('wemap', $name, $name, 'manage_options', 'wemap_connexion', array($this, $html_menu));
    }

    public function wemap_html() {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        ?>
        <br>
        You can also find more information on
        <a href="https://wemap.zendesk.com/hc/en-us" target="_blank">Wemap Help Center</a>
        <br>
        <h3>How to create a pinpoint</h3>
        To create a Pinpoint, you must fill in the red fields (as shown in the example) are mandatory. You need to fill in the "Picture" property if you want to create a Picpoint, and the "Category" property if you need to create a Catpoint, then post.
        <br><br>
        <img src="<?php echo Admin_Wemap_Meta_Box::wemap_path_plugin('images/tuto_site1.png');?>"/>
        <br><br>
        <h3>How to add a Connected Map to the post</h3>
        Click on the button "Embed map in your post".
        <br><br>
        <img src="<?php echo Admin_Wemap_Meta_Box::wemap_path_plugin('images/tuto_site2.png');?>"/>
        <br><br>
        We must now choose Livemap you want to display, you can also choose the size, then click on "Select".
        <br><br>
        <img src="<?php echo Admin_Wemap_Meta_Box::wemap_path_plugin('images/tuto_site3.png');?>"/>
        <br><br>
        A shortcode will appear in the post contents.
        <br><br>
        <code>[livemap width='600' height='400' src='at55ad1ff5b3e323.03163161&emmid=1160']</code>
        <h3>How to add the Pinpoint to the post.</h3>
        When you have created your post, edit it and then click on the button "Add smart map to your post".
        <br><br>
        <img src="<?php echo Admin_Wemap_Meta_Box::wemap_path_plugin('images/tuto_site4.png');?>"/>
        <br><br>
        A shortcode will appear in the post contents.
        <br><br>
        <code>[mini_livemap width='600' height='400' src='504373&token=at55ad1ff5b3e323.03163161']</code>
        <?php
    }

    public function wemap_deconnexion_html() {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        ?>
        <form action="admin.php?page=wemap" method="post">
            <p><input type="submit" name="cf-deconnexion" value="Logout"></p>
        </form>
        <?php
    }

    public function wemap_connexion_html() {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        ?>
        <form action="admin.php?page=wemap" method="post">
            <p>
                Username <br/>
                <input type="text" name="cf-username" value="" size="40" />
            </p>
            <p>
                Password <br/>
                <input type="password" name="cf-pwd" value="" size="40" />
            </p>
            <p><input type="submit" name="cf-connexion" value="Log in"></p>
            <a href=https://getwemap.com/signup >Register</a>
        </form>
        <?php
    }

    public function wemap_co() {
        if (isset($_POST['cf-connexion'])) {
            $connect_serv = new Connect_To_Serv();
            $oauth_token = $connect_serv->get_oauth_token(sanitize_text_field($_POST['cf-username']), sanitize_text_field($_POST['cf-pwd']));

            if ($oauth_token) {
                Connect_To_Serv::wemap_create_cookie('oauth2_token_wemap', $oauth_token);
                $user = $connect_serv->wemap_get_requet('/v3.0/users', $oauth_token);
                $_SESSION['user'] = json_decode($user)->results[0];

                if($_SESSION['user']->role == 0){
                    echo "<script>alert('You are not pro user');</script>";
                    return;
                }

                $token = json_decode($connect_serv->wemap_get_requet('/v3.0/users/token', $oauth_token));
                if ($token != false) {
                    Connect_To_Serv::wemap_create_cookie('token_wemap', $token->token);
                    echo "<script>alert('Success ! You are now connected.');</script>";
                } else {
                    echo "<script>alert('You are not pro user');</script>";
                }
            } else {
                echo "<script>alert('Wrong username or password');</script>";
            }
        } else if (isset($_POST['cf-deconnexion'])) {
            session_cache_expire(0);
            Connect_To_Serv::wemap_unset_cookie('oauth2_token_wemap');
            Connect_To_Serv::wemap_unset_cookie('token_wemap');
        }
    }
}
