<?php

namespace D3R\Event\Store;

class Influxdb extends Base
{
    /**
     * Events to insert in batch mode
     *
     * @param \D3R\Event[]
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_batchEvents;

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->_defaults = array(
                'database'      => false,
                'username'      => false,
                'password'      => false,
                'hostname'      => 'localhost',
                'port'          => 8086
            );
    }

    public function write(\D3R\Event $event)
    {
        $db = $this->db();

        $data           = $event->getData();
        $data['time']   = $event->getTimestamp();

        try {
            $db->insert($event->getName(), $data);
        }
        catch (Exception $ex) {
            throw new \D3R\Exception($ex->getMessage());
        }

        return true;
    }

    /**
     * Write an event in batch mode
     *
     * This adds the event to an array but does not write
     * it to InfluxDB until commitBatch() is called
     *
     * @param \D3R\Event
     * @return $this
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function batchWrite(\D3R\Event $event)
    {
        if (!is_array($this->_batchEvents)) {
            $this->_batchEvents = array();
        }
        if (!isset($this->_batchEvents[$event->getName()])) {
            $this->_batchEvents[$event->getName()] = array();
        }
        $this->_batchEvents[$event->getName()][] = $event;

        return true;
    }

    /**
     * Write pending batches of data
     *
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function commitBatch()
    {
        if (empty($this->_batchEvents)) {
            return true;
        }

        $db = $this->db();

        try {

            foreach ($this->_batchEvents as $name => $events) {
                if (!empty($events)) {
                    continue;
                }

                $data = array();
                foreach ($events as $event) {
                    $datum          = $event->getData();
                    $datum['time']  = $event->getTimestamp();

                    $data[]         = $datum;
                }
                $db->insert($name, $data);
            }
        }
        catch (Exception $ex) {
            throw new \D3R\Exception($ex->getMessage());
        }

        return true;
    }

    /**
     * Get an influxdb db instance
     *
     * @return \crodas\InfluxPHP\DB
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function db()
    {
        try {
            $client = new \crodas\InfluxPHP\Client(
                $this->option('hostname'),
                $this->option('port'),
                $this->option('username'),
                $this->option('password')
            );

            $db = $client->getDatabase($this->option('database'));
        }
        catch (Exception $ex) {
            throw new \D3R\Exception($ex->getMessage());
        }

        return $db;
    }
}
