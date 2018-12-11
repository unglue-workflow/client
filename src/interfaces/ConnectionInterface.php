<?php

namespace unglue\client\interfaces;

/**
 * The connection interface.
 *
 * This interface defines the methods which are required in order to use the connection
 * object inside the controller commands.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
interface ConnectionInterface
{
    /**
     * The test method checks if this connection contains a valid configuration with valid files
     * for a given {{FileHandlerInterface}}.
     *
     * If returns true the connection is keepd and can be used later for the first (or further) iterations.
     *
     * @return boolean Whether the connection (which represents an unglue config) is valid or contains valid values or not.
     */
    public function test();

    /**
     * The iteration methods is used to compile the files based on valid {{FileHandlerInterface}} objects.
     *
     * The force property is used to ensure the first compile process starts after connection test was sucessfull.
     *
     * @param boolean $force If force is enabled the connection must call the {{FileHandlerInterface::handleUpload()}} method.
     * @return boolean
     */
    public function iterate($force = false);
}
