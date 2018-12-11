<?php

namespace unglue\client\controllers;

use unglue\client\base\BaseCompileController;

/**
 * Compile all files once.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class CompileController extends BaseCompileController
{
    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $this->createConnections();

        return self::EXIT_CODE_NORMAL;
    }
}
