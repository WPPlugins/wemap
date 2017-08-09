<?php
class Connect_To_Serv {
    private $address = WEMAP_API_URL;

    private function file_get_contents($url, $method, $content, $oauth2_token='')
    {
        $oauth2_token = isset($_COOKIE['oauth2_token_wemap']) ? $_COOKIE['oauth2_token_wemap']: $oauth2_token;
        if (empty($oauth2_token)){
            return false;
        }

        $opts = array(
            'http'=>array(
                'method' => $method,
                'header' => 'Authorization: Bearer ' . $oauth2_token . "\r\n" .
                'Content-Type: application/json;charset=UTF-8',
                'content' => $content)
        );
        $context = stream_context_create($opts);
        return @file_get_contents($this->address . $url, false, $context);
    }


    public function get_oauth_token($username, $password){
        $content = http_build_query(array(
                'grant_type'=>'password',
                'client_id'=>'PHhGZcIUf73TSc78kpJ7emvEjT5nd22gVL98dGJt',
                'username'=>$username,
                'password'=>$password
        ));

        $opts = array(
            'http'=>array(
                'method' => 'POST',
                'Content-Type: application/x-www-form-urlencoded',
                'content' => $content)
        );

        $context = stream_context_create($opts);
        $responce = @file_get_contents($this->address . '/v3.0/oauth2/token/', false, $context);
        if ($responce){
            $responce = json_decode($responce);
            return $responce->access_token;
        }
        return false;
    }

    public function wemap_get_requet($url, $oauth2_token='') {
        $file = $this->file_get_contents($url, 'GET', '', $oauth2_token);
        return ($file);
    }

    public function wemap_post_requet($url, $content) {
        $file = $this->file_get_contents($url, 'POST', $content);
        return ($file);
    }

    public function wemap_put_requet($url, $content) {
        $file = $this->file_get_contents($url, 'PUT', $content);
        return ($file);
    }

    public function get_media_json($FILE){
        return array(
            'content'=> base64_encode(file_get_contents($FILE['tmp_name'])),
            'name'=> $FILE['name'],
            'type'=> $FILE['type']
        );
    }

    static public function wemap_create_cookie($name, $value) {
        setcookie($name, $value, time()+3600);
    }

    static public function wemap_unset_cookie($name) {
        unset($_COOKIE[$name]);
        setcookie($name, null, time()-3600);
    }

    static public function wemap_start_session()
    {
        session_cache_limiter('private');
        session_cache_expire(60);
        session_start();
    }
}
?>