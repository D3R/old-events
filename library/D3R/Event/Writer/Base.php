<?php
/**
 * The Writer classes take an event and write it to a
 * data store. The data store could be a file on disk
 * or a data store like Influx DB, Elasticsearch or
 * something else
 *
 */

namespace D3R\Event\Writer;

abstract class Base
{
    /**
     * Standard factory method to get a writer instance
     *
     * @param string $type The type of writer to get
     * @param array $options Optional settings for the specified writer
     * @return \D3R\Event\Writer
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function Factory($type, $options = array())
    {
        $class = "\D3R\Event\Writer\\" . ucfirst(strtolower($type));
        // @TODO Remove var_dump
        var_dump($class);
        if (!class_exists($class)) {
            throw new \D3R\Exception('Invalid writer ' . $type);
        }
        $writer = new $class($options);

        return $writer;
    }

    /**
     * A set of key => value options for this writer
     *
     * @var array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_options = array();

    /**
     * Default options to use for this writer
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
     * Write an event to this writers data store
     *
     * @param \D3R\Event $event
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    abstract public function write(\D3R\Event $event);

}
