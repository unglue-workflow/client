<?php

namespace fwcc\client\controllers;

class CompileController extends BaseCompileController
{
    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $this->initConfigsAndTest();
    }
}