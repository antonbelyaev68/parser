<?php

namespace App\Services;

use Behat\Mink\Session;

abstract class Parser
{
    protected $zipCode;

    /** @var Session $session */
    protected $session;

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

    abstract protected function parse();

}