<?php
session_start();
require "vendor/autoload.php";

use GuzzleHttp\Client;
$user_name = '';

if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
    //echo 'token=' . $token;
    try {
        $api = $_SESSION['api'];
        
        if ($api == 'google') {
            $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $token;

            $client  = new Client ();
            $response = $client->request ("GET", $url, [
                    'verify' => false, 
                    'http_errors' => false
                ]);
            switch ($response->getStatusCode()) {
                case 200: 
                    $data = json_decode($response->getBody());
                    $user_name = $data->given_name;
                    break;
                case 401:
                    $_SESSION['token'] = '';
                    header ('Location: OAuthRequest.php?api=google');
                    die;
            }            
        }
        elseif ($api == 'facebook') {
            $url = 'https://graph.facebook.com/v2.8/me?fields=id,name,email,location&access_token=' . $token;

            $client  = new Client ();
            $response = $client->request ("GET", $url, [
                    'verify' => false, 
                    'http_errors' => false
                ]);
            switch ($response->getStatusCode()) {
                case 200: 
                    $data = json_decode($response->getBody());
                    $user_name = $data->name;
                    break;
                case 401:
                    $_SESSION['token'] = '';
                    header ('Location: OAuthRequest.php?api=facebook');
                    die;
            }  
        }

    }
    catch (RequestException $e) {

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lab-OAuth</title>
</head>

<body>
    <?php if (!isset($_SESSION['token'])) { ?>
      Entrar com o Login do: <a href="OAuthRequest.php?api=google">Google</a> | 
                             <a href="OAuthRequest.php?api=facebook">Facebook</a> | 
                             <a href="OAuthRequest.php?api=twitter">Twitter</a>
    <?php } else { ?> 
      Olá <?= $user_name ?>! <br/>
      <a href="logout.php">Logout</a>
    <?php }  ?>
</body>

</html>