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
use WebDriver\Exception;


class ParserListsource extends Parser
{
    protected $url = 'http://www.listsource.com/homepage/index.htm';

    public function parse()
    {
        $driver = new Selenium2Driver('chrome', null, 'http://192.168.99.100:32786/wd/hub');
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

        $locator = $this->waitUntilExist($page, 'locator'); // working javascript
        $locator->selectOption("ZIP_CODE");

        $zipTextArea = $this->waitUntilExist($page, 'zipTextArea'); // working javascript
        $zipTextArea->setValue($this->zipCode);

        $button = $page->find('xpath', '//button');
        $button->click(); // add zip code

        $this->waitUntilExist($page, 'CRITERIA_ZIP_CODE_'.$this->zipCode); // working javascript

        $propertyLink = $page->find('named', ['id_or_name', 'img_PROPERTY']);
        $propertyLink->click(); // click property in left menu
        $this->session->wait(1);

        $page->find('named', ['id_or_name', 'PROPERTY_PAGE_IMG'])->click(); // click tab property
        $this->session->wait(1);

        $page->find('named', ['id_or_name', 'locator_prop'])->selectOption("PROPERTY_TYPE");

        $locatorAvailableList_prop = $this->waitUntilExist($page, 'locatorAvailableList_prop'); // working javascript
        $this->session->wait(5);
        $this->scrin(1);
        $locatorAvailableList_prop->selectOption(16, true);

        $page->find('named', ['id_or_name', 'prop_add'])->click();
        $this->session->wait(5);

        $count = $this->parceCount($page);
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

        $result['f'] = $countNewNew; //F in excel

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $foreClosureLink = $page->find('named', ['id_or_name', 'FORECLOSURE_PAGE_IMG'])->click();
        $this->session->wait(1);

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure'])->selectOption('FORE_D_PUB_DT'); // select recent added date

        $this->waitUntilExist($page, 'last6_FORE_D_PUB_DT'); // working javascript

        $this->session->executeScript("document.getElementById('last6_FORE_D_PUB_DT').click()");
        $this->waitUntilDisabled($foreClosureSelect);

        $page->find('named', ['id_or_name', 'mort_add_FORE_D_PUB_DT'])->click();

        $dateInputFromValue = $page->find('named', ['id_or_name', 'fromValue_FORE_D_PUB_DT'])->getValue();
        $dateInputToValue = $page->find('named', ['id_or_name', 'toValue_FORE_D_PUB_DT'])->getValue();

        $this->waitUntilExist($page, 'CRITERIA_FORE_D_PUB_DT_'.$dateInputFromValue.'-'.$dateInputToValue); // working javascript

        $count = $page->find('named', ['id_or_name', 'td_0']);
        $result['h'] = $count->getText();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->scrin(3);
        $bankOwned = $page->find('named', ['id_or_name', 'foreClosurePosition']);
        $bankOwned->selectOption('BANKOWNED_FORECLOSURE');

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $this->waitUntilDisabled($foreClosureSelect);

        $foreClosureSelect->selectOption('FORE_R_PUB_DT'); // select recent added date

        $this->waitUntilExist($page, 'last6_FORE_R_PUB_DT'); // working javascript

        $this->session->executeScript("document.getElementById('last6_FORE_R_PUB_DT').click()")->click();
        $this->waitUntilDisabled($foreClosureSelect);

        $page->find('named', ['id_or_name', 'mort_add_FORE_R_PUB_DT']);

        $dateInputFromValue = $page->find('named', ['id_or_name', 'fromValue_FORE_R_PUB_DT'])->getValue();
        $dateInputToValue = $page->find('named', ['id_or_name', 'toValue_FORE_R_PUB_DT'])->getValue();
        $this->scrin(5);
        $this->waitUntilExist($page, 'CRITERIA_FORE_R_PUB_DT_'.$dateInputFromValue.'-'.$dateInputToValue); // working javascript

        $td0 = $page->find('named', ['id_or_name', 'td_0']);
        $td1 = $page->find('named', ['id_or_name', 'td_1']);
        $result['k'] = $td0->getText().$td1->getText();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->scrin(6);
        $default = $page->find('named', ['id_or_name', 'foreClosurePosition']);
        $default->selectOption('DEFAULT_FORECLOSURE');

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $this->waitUntilDisabled($foreClosureSelect);

        $foreClosureLink = $page->find('named', ['id_or_name', 'OPTIONS_PAGE_IMG'])->click();
        $this->session->wait(1);

        $ownerOccupiedStatus = $page->find('named', ['id_or_name', 'ownerOption']);
        $ownerOccupiedStatus->selectOption('OPT_EXCLUDE_OWNER_OCCUPIED');

        $this->waitUntilExist($page, 'CRITERIA_OWNER-OCCUPIED'); // working javascript

        $criteria = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->scrin(7);
        $criteria->selectOption("EQUITY_PCT");

        $this->waitUntilExist($page, 'fromValue_prop'); // working javascript

        $page->find('named', ['id_or_name', 'fromValue_prop'])->setValue(99);
        $page->find('named', ['id_or_name', 'toValue_prop'])->setValue(100);
        $page->find('named', ['id_or_name', 'addButton_prop'])->click();

        $this->waitUntilExist($page, 'CRITERIA_EQUITY_PCT'); // working javascript

        $page->find('named', ['id_or_name', 'locator_prop'])->selectOption('LAST_SALE_DATE');

        $this->waitUntilExist($page, 'selBox_LAST_SALE_DATE'); // working javascript

        $this->session->executeScript("document.getElementById('last6_LAST_SALE_DATE').click()");
        $page->clickLink('prop_add');

        $this->waitUntilExist($page, 'CRITERIA_LAST_SALE_DATE'); // working javascript

        $count = $page->find('named', ['id_or_name', 'td_0']);
        $result['c'] = $count->getText();

        file_put_contents("scrin!!!.png", $this->session->getScreenshot());


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        VarDumper::dump($result);

        exit;

        VarDumper::dump($a->getXpath());
        VarDumper::dump($a->getOuterHtml());
        file_put_contents("scrin.png", $this->session->getScreenshot());
        VarDumper::dump($locatorAvailableList_prop->getOuterHtml());
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
            try {
                $val = $sing->getText();
            } catch (Exception $e) {
                $val = 0;
            }

        } else {
            $val = 0;
        }
        return $val;
    }
}