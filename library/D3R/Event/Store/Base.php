<?php
/**
 * The Store classes take an event and write it to a
 * data store. The data store could be a file on disk
 * or a data store like Influx DB, Elasticsearch or
 * something else
 *
 */

namespace D3R\Event\Store;

abstract class Base
{
    /**
     * Standard factory method to get a storage instance
     *
     * @param string $type The type of storage to get
     * @param array $options Optional settings for the specified storage
     * @return \D3R\Event\Store
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function Factory($type, $options = array())
    {
        $class = "\D3R\Event\Store\\" . ucfirst(strtolower($type));
        if (!class_exists($class)) {
            throw new \D3R\Exception('Invalid storage ' . $type);
        }
        $storage = new $class($options);

        return $storage;
    }

    /**
     * A set of key => value options for this storage
     *
     * @var array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_options = array();

    /**
     * Default options to use for this storage
     *
     * @var array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_defaults = array();

    /**
     * Class constructor
     *
     * @param array $options
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Get an option by key
     *
     * @param string $key
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function option($key)
    {
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }
        if (isset($this->_defaults[$key])) {
            return $this->_defaults[$key];
        }
        return null;
    }

    /**
     * Write an event to this data store
     *
     * @param \D3R\Event $event
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    abstract public function write(\D3R\Event $event);

}
