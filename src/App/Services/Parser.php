<?php

namespace App\Services;

use Behat\Mink\Session;

abstract class Parser
{
    protected $zipCodes;

    /** @var Session $session */
    protected $session;

    /**
     * @return mixed
     */
    public function getZipCodes()
    {
        return $this->zipCodes;
    }

    /**
     * @param mixed $zipCodes
     */
    public function setZipCodes($zipCodes)
    {
        $this->zipCodes = $zipCodes;
    }

    abstract protected function parse();

}