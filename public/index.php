<?php
require_once __DIR__."/../start.php";

if(isset($_POST["action"]) && in_array($_POST["action"], [
    "build", "deploy",
    ])) {
    $action = json_encode([$_POST["action"], time()]);

    redis()->lpush(env("QUEUE_NAME"),[$action]);
    echo json_encode(["success" => true]);
}else{
    echo json_encode(["success" => false]);
}
