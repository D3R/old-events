<?php

namespace D3R;

class Event
{
    /**
     * Factory method for creating an event
     *
     * This is a convenience method so that we can
     * take advantage of the fluent interface more
     * easily.
     *
     * The name of the event is a classification,
     * eg: mysite.errors, server01.cpu.0
     *
     * @param string $name The name of the event
     * @return \D3R\Event
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function Factory($name)
    {
        return new static($name);
    }

    /**
     * The name of this event
     *
     * @var string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_name;

    /**
     * This array stores the data name value pairs for this event
     *
     * @param array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_data;

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Get the name for this event
     *
     * @return string
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Standard magic get to get a data value from this event
     *
     * @param string $key
     * @return mixed
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __get($key)
    {
        return $this->_data[$key];
    }

    /**
     * Get all the data for this event as an array
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Standard magic set to set a data value on this event
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Set a data variable on this event
     *
     * @param string $key
     * @param mixed $value
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
}
