<?php

namespace App\Services;

use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;

abstract class Parser
{
    protected $zipCode;
    protected $matrixResult;

    /** @var Session $session */
    protected $session = false;

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

    public function setMatrixResult($result)
    {
        $this->matrixResult = $result;
    }

    protected function createSession()
    {
        $driver = new Selenium2Driver('chrome', null, 'http://192.168.99.100:32773/wd/hub');
        $this->session = new Session($driver);
        $this->session->start();
    }

    protected function scrin($name)
    {
        file_put_contents($name.".png", $this->session->getScreenshot());
    }

    protected function waitUntilDisabled($object)
    {
        $js = "";
        while (!is_null($js)) {
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

    protected function removeCriteria($page, $id)
    {
        $el = $page->find('named', ['id_or_name', $id]);
        $el->click();
        $this->waitUntilNull($page, $id);
    }
}