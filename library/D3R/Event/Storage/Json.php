<?php

namespace D3R\Event\Storage;

class Json extends Base
{
    /**
     * Read a json file and return an Event object from it
     *
     * @param string $filename
     * @return \D3R\Event
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public static function read($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new \D3R\Exception('Unable to read from ' . $filename);
        }

        $data = file_get_contents($filename);
        $json = json_decode($data);

        $event = \D3R\Event::Factory($json->name)
                    ->setTimestamp($json->timestamp)
                    ;
        foreach ($json->data as $key => $value) {
            $event->set($key, $value);
        }

        return $event;
    }

    /**
     * Class constructor
     *
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->_defaults = array(
                'mode' => 0750
            );
    }

    public function write(\D3R\Event $event)
    {
        $directory = $this->option('directory');
        if (empty($directory)) {
            throw new \D3R\Exception('Json : No directory provided');
        }

        $directory = $this->checkDirectory($directory);

        $data = array(
            'name'          => $event->getName(),
            'timestamp'     => $event->getTimestamp(),
            'data'          => $event->getData()
        );

        $hash = md5($event->getTimestamp() . $event->getName() . mt_rand(0,1000));
        $json = json_encode($data) . "\n";

        if (!file_put_contents($directory . '/' . $event->getName() . '.' . $hash . '.json', $json)) {
            throw new \D3R\Exception("Unable to write event");
        }

        return true;
    }

    /**
     * Ensure the base directory exists
     *
     * @param string $directory
     * @return boolean
     * @throws \D3R\Exception
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, $this->option('mode'))) {
                throw new \D3R\Exception("Json : Unable to create directory " . $directory);
            }
        }

        $directory = trim($directory);
        $directory = rtrim($directory, '/');

        return $directory;
    }
}
