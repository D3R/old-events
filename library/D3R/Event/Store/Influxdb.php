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

    /**
     * A cached curl handle
     *
     * @var resource
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_curlHandle = null;

    /**
     * Last status code
     *
     * @var array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected $_lastCurlStatus = null;

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->_defaults = array(
                'database'      => false,
                'username'      => false,
                'password'      => false,
                'hostname'      => 'localhost',
                'port'          => 8086,
                'verify_peer'   => false,
            );
    }

    public function write(\D3R\Event $event)
    {
        // $db = $this->db();
        try {
            // $db->insert($event->getName(), $data);
            $columns        = $event->getDataKeys();
            $columns[]      = 'time';
            $data           = array_values($event->getData());
            $data[]         = $event->getTimestamp();

            $name = $event->getName();
            $tags = $event->getTags();
            array_unshift($tags, $name);

            foreach ($tags as $tag) {
                $this->send($tag, $columns, array($data));
            }
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
    // public function batchWrite(\D3R\Event $event)
    // {
    //     if (!is_array($this->_batchEvents)) {
    //         $this->_batchEvents = array();
    //     }
    //     if (!isset($this->_batchEvents[$event->getName()])) {
    //         $this->_batchEvents[$event->getName()] = array();
    //     }
    //     $this->_batchEvents[$event->getName()][] = $event;

    //     return true;
    // }

    /**
     * Write pending batches of data
     *
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    // public function commitBatch()
    // {
    //     if (empty($this->_batchEvents)) {
    //         return true;
    //     }

    //     $db = $this->db();

    //     try {

    //         foreach ($this->_batchEvents as $name => $events) {
    //             if (!empty($events)) {
    //                 continue;
    //             }

    //             $data = array();
    //             foreach ($events as $event) {
    //                 $datum          = $event->getData();
    //                 $datum['time']  = $event->getTimestamp();

    //                 $data[]         = $datum;
    //             }
    //             $db->insert($name, $data);
    //         }
    //     }
    //     catch (Exception $ex) {
    //         throw new \D3R\Exception($ex->getMessage());
    //     }

    //     return true;
    // }

    /**
     * Write a dataset to InfluxDB
     *
     * @param string $name
     * @param array $columns
     * @param array $data
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function send($name, $columns, $data)
    {
        $payload = array(
                'name'      => $name,
                'columns'   => $columns,
                'points'    => $data,
            );

        $url        = 'http://' . $this->option('hostname') . ':' . $this->option('port') . '/db/' . $this->option('database') . '/series?u=' . $this->option('username') . '&p=' . $this->option('password');
        $payload    = json_encode(array($payload));
        $curl       = $this->curl();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($curl);
        if (false === $result) {
            throw new \D3R\Exception(curl_error($curl), curl_errno($curl));
        }

        $this->_lastCurlStatus = curl_getinfo($curl);
        if (!$this->success((int) $this->_lastCurlStatus['http_code'])) {
            throw new \D3R\Exception('Error writing event to InfluxDB');
        }

        return true;
    }

    /**
     * Does the given status code indicate success
     *
     * @param int
     * @return boolean
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function success($int)
    {
        switch ($int) {
            case 200:
                return true;

            default:
                return false;
        }
    }

    /**
     * Get a curl handle
     *
     * @return resource
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function curl()
    {
        if (!is_resource($this->_curlHandle)) {
            $credentials    = $this->option('username') . ':' . $this->option('password');
            $curl           = curl_init();
            $options        = array(
                    // CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
                    // CURLOPT_USERPWD             => $credentials,
                    // CURLOPT_SSL_VERIFYPEER      => $this->option('verify_peer'),
                    CURLOPT_RETURNTRANSFER      => true,
                    CURLINFO_HEADER_OUT         => true,
                );
            if (!curl_setopt_array($curl, $options)) {
                throw new \D3R\Exception('Unable to create curl connection');
            }

            $this->_curlHandle = $curl;
        }
        return $this->_curlHandle;
    }
}
