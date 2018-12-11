<?php

namespace unglue\client\interfaces;

use unglue\client\tasks\ConfigConnection;

interface FileHandlerInterface
{
    public function __construct(ConfigConnection $configConnection);

    public function init();

    public function count();

    public function iterate($force);

    public function handleUpload();

    public function name();
}
