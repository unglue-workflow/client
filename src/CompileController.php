<?php

namespace fwcc\client;


class CompileController extends BaseCompileController
{
    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $this->initConfigsAndTest();

        
    }
}