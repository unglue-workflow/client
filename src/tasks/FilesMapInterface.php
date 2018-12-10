<?php

namespace unglue\client\tasks;

interface FilesMapInterface
{
    public function __construct(ConfigConnection $configConnection);

    public function init();

    public function count();

    public function iterate($force);

    public function handleUpload();

    public function name();
}