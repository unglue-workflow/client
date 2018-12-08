<?php

namespace unglue\client\controllers;

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
    public $timeout = 300000;

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'timeout',
        ]);
    }

    public function actionIndex($path = null)
    {
        $this->setFolder($path);
        $connections = $this->createConnections();

        // infinite loop
        while (true) {
            // iterator rough all connections
            foreach ($connections as $con) {
                $con->iterate();
            }
            // cleanup memory
            gc_collect_cycles();
            // sleep
            usleep($this->timeout);
        }
    }
}
