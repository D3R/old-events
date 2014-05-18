<?php

namespace D3R\Event\Store;

class Influxdb extends Base
{
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
