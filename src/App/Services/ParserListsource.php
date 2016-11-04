<?php

namespace App\Services;

use App\Services\Parser;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\Goutte\Client as GoutteClient;
use Behat\Mink\Element\NodeElement;
use Symfony\Component\VarDumper\VarDumper;


class ParserListsource extends Parser
{
    protected $url = 'http://www.listsource.com/homepage/index.htm';

    public function parse()
    {
        $driver = new Selenium2Driver('chrome', null, 'http://192.168.99.100:32795/wd/hub');
        $this->session = new Session($driver);
        $this->session->start();
        $this->session->visit($this->url);
        $page = $this->session->getPage();

        $as = $page->findAll('xpath', '//a');

        foreach ($as as $key => $a) {
            if ($key == 14) {
                $a->click(); #search properties
            }
        }

        $locator = null;
        while (empty($locator)) { // working javascript
            $locator = $page->find('named', ['id_or_name', 'locator']);
            $this->session->wait(1);
        }

        $locator->selectOption("ZIP_CODE");

        $zipTextArea = null;
        while (empty($zipTextArea)) { // working javascript
            $zipTextArea= $page->find('named', ['id_or_name', 'zipTextArea']);
            $this->session->wait(1);
        }

        $zipTextArea->setValue($this->zipCode);

        $button = $page->find('xpath', '//button');
        $button->click();

        $js = null;
        while (empty($zipTextArea)) { // working javascript
            $js= $page->find('named', ['id_or_name', 'CRITERIA_ZIP_CODE_30236']);
            $this->session->wait(1);
        }

        $propertyIcon = $page->find('named', ['id_or_name', 'img_PROPERTY']);
        $propertyIcon->click();

        $propertyLink = $page->find('named', ['id_or_name', 'PROPERTY_PAGE_IMG']);
        $propertyLink->click();
        $this->session->wait(1);


        VarDumper::dump($button->getOuterHtml());

        #file_put_contents("scrin.png", $this->session->getScreenshot());

        exit;

        VarDumper::dump($key);
        VarDumper::dump($a->getXpath());
        VarDumper::dump($a->getOuterHtml());
    }


}