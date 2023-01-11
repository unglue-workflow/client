<?php

namespace unglue\client\controllers;

use unglue\client\base\BaseCompileController;
use yii\console\ExitCode;

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

        return ExitCode::OK;
    }
}
