<?php

namespace App\Services;

use Behat\Mink\Session;

abstract class Parser
{
    protected $zipCode;

    /** @var Session $session */
    protected $session;

    abstract protected function parse();

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCodes
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    protected function scrin($name)
    {
        file_put_contents($name.".png", $this->session->getScreenshot());
    }

    protected function waitUntilDisabled($object)
    {
        $js = "";
        while (!is_null($js)) { // working javascript
            $js = $object->getAttribute('disabled');
            $this->session->wait(1);
        }
        return true;
    }

    protected function waitUntilExist($page, $id)
    {
        $object = null;
        while (empty($object)) {
            $object = $page->find('named', ['id_or_name', $id]);
            $this->session->wait(1);
        }
        return $object;
    }

    protected function waitUntilNull($page, $id)
    {
        $object = true;
        while (!empty($object)) {
            $object = $page->find('named', ['id_or_name', $id]);
            $this->session->wait(1);
        }
        return $object;
    }
}