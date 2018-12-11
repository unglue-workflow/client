<?php

namespace unglue\client\interfaces;

use unglue\client\tasks\ConfigConnection;

/**
 * File Handler Interface.
 *
 * The file handler interface contains all required methods in order to handle:
 *
 * + API request
 * + counting the map
 * + Initializer to assign data.
 * + Iterator which triggers the handleUpload
 * + A name method which is used to provide output infos.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
interface FileHandlerInterface
{
    /**
     * Class constructor must provider the Config Connection object.
     *
     * @param ConfigConnection $configConnection
     */
    public function __construct(ConfigConnection $configConnection);

    /**
     * Contains a name for output informations. Like `js` or `css` in order to make
     * log easy to read.
     *
     * @return string
     */
    public function name();

    /**
     * The initalizer is called by the ConfigConnection before {{test()}} method runs
     * in order to assign map data which then is returned in the count() method.
     */
    public function init();

    /**
     * The count method returns the number of files to watch. If count is 0 this file handler
     * object will be destroyed.
     *
     * @return integer
     */
    public function count();

    /**
     * The iteration process is triggered when ever compile should be run or not. If force is enabled
     * compile must be run.
     *
     * @param boolean $force Whether compiling is force or not. If not the file handler will check for changed files.
     * @return boolean
     */
    public function iterate($force);

    /**
     * Handle the file upload to the API.
     *
     * @return boolean Whether upload/request was sucessfull or not.
     */
    public function handleUpload();
}
