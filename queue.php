<?php
require_once "start.php";
echo "started at ".date("Y-m-d H:i:s")."\n";
while(true) {
    $value = redis()->rpop(env("QUEUE_NAME"));
    if (!empty($value)) {
        try {
            $decoded = json_decode($value);
            if (empty($decoded)) {
                throw new Exception("decode fail=>$value");
            }
            list($action, $time) = $decoded;
            $enqueuedAt = date("Y-m-d H:i:s", $time);
            $workDir = env("WORK_DIR");
            $dockerRegistry = env("DOCKER_REGISTRY");
            $image = env("IMAGE");
            $service = env("SERVICE");
            switch ($action) {
                case "deploy":
                    $command = __DIR__ . "/scripts/$action.sh $workDir $service";
                    break;
                case "build":
                    $command = __DIR__ . "/scripts/$action.sh $workDir $image $dockerRegistry";
                    break;
                default:
                    throw new Exception("unknown action=>$value");
            }
            echo date("Y-m-d H:i:s") . ":start job($action) from [$enqueuedAt]\n";
            //echo "$command\n";
            exec("bash $command 2>&1", $output, $status);
            if (!empty($status)) {
                throw new Exception("command($command) fail=>" . print_r($output, 1) . "\n");
            }
            echo date("Y-m-d H:i:s") . ":finished job($action) from [$enqueuedAt]" . print_r($output, 1) . "\n";
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo date("Y-m-d H:i:s") . ":failed=>$message\n";
        }
    }
    sleep(1);
}
