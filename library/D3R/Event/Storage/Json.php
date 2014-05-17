<?php

namespace D3R\Event\Storage;

class Json extends Base
{
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
