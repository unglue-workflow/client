<?php

namespace fwcc\client\controllers;

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

            gc_collect_cycles();
            usleep(300000);
        }
    }
}