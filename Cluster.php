<?php

$hosts = [
    'redis_1:6379',
    'redis_2:6379',
    'redis_3:6379',
    'redis_4:6379',
    'redis_5:6379',
    'redis_6:6379'
];

$usePersistence = true;
// setup keys
echo "Setting up test".PHP_EOL;

try {
    $obj_cluster = new RedisCluster(NULL, $hosts, 10, 10);

    echo "Setting all keys";

    for ($i = 0; $i < 128; $i++) {
        $result = $obj_cluster->set("key".$i, (string)$i);
        if (!$result) {
            var_dump("unable to set key");
            exit;
        }
        echo $result ? "Key set ".$i.PHP_EOL : "Key failed ".$i.PHP_EOL;
    }
    $obj_cluster->close();


    //start test
    echo "Testing!".PHP_EOL;
    $obj_cluster = new RedisCluster(null, $hosts, 5, 5, $usePersistence);
    while (true) {
        try {
            $key = rand(0, 127);
            $value = $obj_cluster->get("key".$key);
            if ((string)$value !== (string)$key) {
                if (!$value) {
                    continue;
                }
                echo "Wrong value found!!".PHP_EOL;
                echo "Key: ".$key.PHP_EOL;
                echo "Value: ".$value.PHP_EOL;
                $obj_cluster->close();
                $obj_cluster = new RedisCluster(null, $hosts, 5, 5, $usePersistence);
            }

        } catch (Exception $e) {
            //echo 'Caught exception: ', $e->getMessage(), "\n";
            //echo "skipped".PHP_EOL;
        }
        //
    }
} catch (RedisClusterException $ex) {
    var_dump($ex);exit;
}