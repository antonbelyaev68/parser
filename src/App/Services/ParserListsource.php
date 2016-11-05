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
        $driver = new Selenium2Driver('chrome', null, 'http://192.168.99.100:32777/wd/hub');
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
        $button->click(); // add zip code

        $js = null;
        while (empty($js)) { // working javascript
            $js= $page->find('named', ['id_or_name', 'CRITERIA_ZIP_CODE_'.$this->zipCode]);
            $this->session->wait(1);
        }

        $propertyLink = $page->find('named', ['id_or_name', 'img_PROPERTY']);
        $propertyLink->click(); // click property in left menu
        $this->session->wait(1);

        $propertyLink = $page->find('named', ['id_or_name', 'PROPERTY_PAGE_IMG']);
        $propertyLink->click(); // click tab property
        $this->session->wait(1);

        $criteria = $page->find('named', ['id_or_name', 'locator_prop']);
        $criteria->selectOption("PROPERTY_TYPE");

        $locatorAvailableList_prop = null;
        while (empty($locatorAvailableList_prop)) { // working javascript
            $locatorAvailableList_prop = $page->find('named', ['id_or_name', 'locatorAvailableList_prop']);
            $this->session->wait(1);
        }
        $this->session->wait(5);

        file_put_contents("scrin.png", $this->session->getScreenshot());
        VarDumper::dump($locatorAvailableList_prop->getOuterHtml());

        $count = $this->parceCount($page);

        $locatorAvailableList_prop->selectOption(16, true);
        $prop_add = $page->find('named', ['id_or_name', 'prop_add']);
        $prop_add->click();
        $this->session->wait(5);


        $countNew = $count;
        while ($count == $countNew) { // working javascript
            $countNew = $this->parceCount($page);
            $this->session->wait(5);
        }

        $countNewNew = 0;
        $countOldOld = 1;
        while ($countNewNew != $countOldOld) { // working javascript
            $countOldOld = $this->parceCount($page);
            $this->session->wait(5);
            $countNewNew = $this->parceCount($page);
        }

        $result['property_count'] = $countNewNew; //F in excel

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $foreClosureLink = $page->find('named', ['id_or_name', 'FORECLOSURE_PAGE_IMG']);
        $foreClosureLink->click();
        $this->session->wait(1);

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $foreClosureSelect->selectOption('FORE_D_PUB_DT'); // select recent added date

        $last6month = null;
        while (empty($last6month)) { // working javascript
            $last6month= $page->find('named', ['id_or_name', 'last6_FORE_D_PUB_DT']);
            $this->session->wait(1);
        }

        $last6month->click();
        $this->session->wait(1);

        $addButton = $page->find('named', ['id_or_name', 'mort_add_FORE_D_PUB_DT']);
        $addButton->click();

        $dateInputFrom = $page->find('named', ['id_or_name', 'fromValue_FORE_D_PUB_DT']);
        $dateInputFromValue = $dateInputFrom->getValue();
        $dateInputTo = $page->find('named', ['id_or_name', 'toValue_FORE_D_PUB_DT']);
        $dateInputToValue = $dateInputTo->getValue();

        $js = null;
        while (empty($js)) { // working javascript
            $js= $page->find('named', ['id_or_name', 'CRITERIA_FORE_D_PUB_DT_'.$dateInputFromValue.'-'.$dateInputToValue]);
            $this->session->wait(1);
        }

        file_put_contents("scrin.png", $this->session->getScreenshot());
        VarDumper::dump($js->getOuterHtml());

        exit;

        VarDumper::dump($key);
        VarDumper::dump($a->getXpath());
        VarDumper::dump($a->getOuterHtml());
    }

    private function parceCount($page)
    {
        $sing1 = $page->find('named', ['id_or_name', 'td_0']);
        $sing1 = $this->getTextValue($sing1);
        $sing2 = $page->find('named', ['id_or_name', 'td_1']);
        $sing2 = $this->getTextValue($sing2);
        #$sing3 = $page->find('named', ['id_or_name', 'td_2']);
        #$sing3 = $sing3->getText();
        $sing4 = $page->find('named', ['id_or_name', 'td_3']);
        $sing4 = $this->getTextValue($sing4);
        $sing5 = $page->find('named', ['id_or_name', 'td_4']);
        $sing5 = $this->getTextValue($sing5);
        $sing6 = $page->find('named', ['id_or_name', 'td_5']);
        $sing6 = $this->getTextValue($sing6);

        return $sing1.$sing2.$sing4.$sing5.$sing6;
    }

    private function getTextValue($sing)
    {
        if ($sing instanceof NodeElement) {
            $val = $sing->getText();
        } else {
            $val = 0;
        }
        return $val;
    }


}