<?php

namespace fwcc\client;


class WatchController extends BaseCompileController
{
    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $this->initConfigsAndTest();

        while (true) {
            foreach ($this->connections as $con) {
                $con->iterate();
            }

            //$this->outputInfo(microtime(true) . " [".memory_get_usage()."] - Stack");
            gc_collect_cycles();
            usleep(300000);
        }
    }
}