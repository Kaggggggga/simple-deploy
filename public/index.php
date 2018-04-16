<?php
require_once __DIR__."/../start.php";

try {
    if(!isset($_SERVER["HTTP_X_HUB_SIGNATURE"])){
        throw new Exception("signature not found");
    }
    $secret = env("APP_KEY");
    $payload = file_get_contents('php://input');
    if (strpos($payload, 'payload=') === 0) {
        $payload = substr(urldecode($payload), 8);
    }
    $signature = hash_hmac('sha1', $payload, $secret);
    if("sha1=$signature" !== $_SERVER["HTTP_X_HUB_SIGNATURE"]){
        throw new Exception("signature not match=>"
            ."(sha1=$signature)!={$_SERVER["HTTP_X_HUB_SIGNATURE"]}"
            .print_r($_SERVER, 1)
        );
    }
    if(!isset($_SERVER['HTTP_X_GITHUB_EVENT'])
        || $_SERVER['HTTP_X_GITHUB_EVENT'] != "push"){
        throw new Exception("not a push event=>"
            .print_r($_SERVER, 1)
        );
    }

    $action = json_encode(["build", time()]);
    redis()->lpush(env("QUEUE_NAME"), [$action]);
    echo json_encode(["success" => true]);
}catch(Exception $e) {
    $message = $e->getMessage();
    echo json_encode(["success" => false]);
}
