<?php

namespace unglue\client\controllers;

use unglue\client\base\BaseCompileController;

/**
 * Watch for file changes.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class WatchController extends BaseCompileController
{
    /**
     * @var integer A timeout value between checking files in micro seconds.
     */
    public $timeout = 500000;

    /**
     * @var integer The number of iterations until the file map of the connections is newly written. By default this means
     * the files index is written every 5 second (10 * 500000ms). If $reindex is 0 this behavior is turned of an new files are not
     * detected.
     * @since 1.2.0
     */
    public $reindex = 10;

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'timeout', 'reindex',
        ]);
    }

    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $connections = $this->createConnections();

        $index = 0;
        // infinite loop
        while (true) {
            $index++;
            // iterator rough all connections
            foreach ($connections as $con) {
                $con->iterate();
                // re index map if index is equal $this->rendix value
                if ($index === $this->reindex) {
                    $con->reIndexConfigAndHandlerMap();
                    $index = 0;
                }
            }
            // cleanup memory
            gc_collect_cycles();
            // sleep
            usleep($this->timeout);
        }
    }
}
