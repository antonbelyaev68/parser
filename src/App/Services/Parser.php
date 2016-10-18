<?php

namespace App\Services;

abstract class Parser
{
    protected $zipCodes;
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