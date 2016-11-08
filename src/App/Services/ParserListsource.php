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
        $this->createSession();
        $this->session->visit($this->url);
        $page = $this->session->getPage();

        $result['P'] = $this->matrixResult['p'];
        $result['N'] = $this->matrixResult['n'];

        $as = $page->findAll('xpath', '//a');

        foreach ($as as $key => $a) {
            if ($key == 14) {
                $a->click(); #search properties
            }
        }

        $this->waitUntilExist($page, 'locator')->selectOption("ZIP_CODE"); // working javascript

        $zipTextArea = $this->waitUntilExist($page, 'zipTextArea'); // working javascript
        $zipTextArea->setValue($this->zipCode);

        $this->waitUntilDisabled($page->find('named', ['id_or_name', 'locator_prop']));
        $this->scrin(0);
        $page->find('xpath', '//button')->click(); // add zip code
        $this->waitUntilExist($page, 'CRITERIA_ZIP_CODE_'.$this->zipCode); // working javascript

        $page->find('named', ['id_or_name', 'img_PROPERTY'])->click(); // click property in left menu
        $this->session->wait(1);

        $page->find('named', ['id_or_name', 'PROPERTY_PAGE_IMG'])->click(); // click tab property
        $this->session->wait(1);

        $page->find('named', ['id_or_name', 'locator_prop'])->selectOption("PROPERTY_TYPE");

        $locatorAvailableList_prop = $this->waitUntilExist($page, 'locatorAvailableList_prop'); // working javascript
        $this->waitUntilDisabled($locatorAvailableList_prop);
        $this->session->wait(10);
        $this->scrin(1);
        $locatorAvailableList_prop->selectOption(16, true);

        $page->find('named', ['id_or_name', 'prop_add'])->click();
        $this->waitUntilDisabled($page->find('named', ['id_or_name', 'locator_prop']));
        $this->scrin("1-1");

        $count = $this->parceCount($page);
        $countNew = $count;
        $i = 1;
        while ($count == $countNew) { // working javascript
            if ($i == 10) {
                break;
            }
            $countNew = $this->parceCount($page);
            $this->session->wait(5);
            $i++;
        }
        $this->scrin("1-2");

        $countNewNew = 0;
        $countOldOld = 1;
        while ($countNewNew != $countOldOld) { // working javascript
            $countOldOld = $this->parceCount($page);
            $this->session->wait(5);
            $countNewNew = $this->parceCount($page);
        }
        $this->scrin("1-3");

        $result['F'] = $countNewNew; //F in excel
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $page->find('named', ['id_or_name', 'FORECLOSURE_PAGE_IMG'])->click();
        $this->session->wait(1);

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $foreClosureSelect->selectOption('FORE_D_PUB_DT'); // select recent added date

        $this->waitUntilExist($page, 'last6_FORE_D_PUB_DT'); // working javascript
        $this->session->executeScript("document.getElementById('last6_FORE_D_PUB_DT').click()");
        $this->scrin(2);
        $this->waitUntilDisabled($foreClosureSelect);

        $page->find('named', ['id_or_name', 'mort_add_FORE_D_PUB_DT'])->click();

        $dateInputFromValue = $page->find('named', ['id_or_name', 'fromValue_FORE_D_PUB_DT'])->getValue();
        $dateInputToValue = $page->find('named', ['id_or_name', 'toValue_FORE_D_PUB_DT'])->getValue();
        $this->waitUntilExist($page, 'CRITERIA_FORE_D_PUB_DT_'.$dateInputFromValue.'-'.$dateInputToValue); // working javascript

        $result['H'] = $this->parceCount($page);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->scrin(3);
        $bankOwned = $page->find('named', ['id_or_name', 'foreClosurePosition']);
        $bankOwned->selectOption('BANKOWNED_FORECLOSURE');

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $this->waitUntilDisabled($foreClosureSelect);
        $foreClosureSelect->selectOption('FORE_R_PUB_DT'); // select recent added date

        $this->waitUntilExist($page, 'last6_FORE_R_PUB_DT'); // working javascript
        $this->session->executeScript("document.getElementById('last6_FORE_R_PUB_DT').click()");
        $this->waitUntilDisabled($foreClosureSelect);
        $this->scrin(4);
        $page->find('named', ['id_or_name', 'mort_add_FORE_R_PUB_DT'])->click();

        $dateInputFromValue = $page->find('named', ['id_or_name', 'fromValue_FORE_R_PUB_DT'])->getValue();
        $dateInputToValue = $page->find('named', ['id_or_name', 'toValue_FORE_R_PUB_DT'])->getValue();
        $this->scrin(5);
        $this->waitUntilExist($page, 'CRITERIA_FORE_R_PUB_DT_'.$dateInputFromValue.'-'.$dateInputToValue); // working javascript

        $this->scrin("5-1");
        $result['K'] = $this->parceCount($page);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->scrin(6);
        $default = $page->find('named', ['id_or_name', 'foreClosurePosition']);
        $default->selectOption('DEFAULT_FORECLOSURE');

        $foreClosureSelect = $page->find('named', ['id_or_name', 'locator_foreclosure']);
        $this->waitUntilDisabled($foreClosureSelect);

        $page->find('named', ['id_or_name', 'OPTIONS_PAGE_IMG'])->click();
        $this->session->wait(1);

        $ownerOccupiedStatus = $page->find('named', ['id_or_name', 'ownerOption']);
        $ownerOccupiedStatus->selectOption('OPT_EXCLUDE_OWNER_OCCUPIED');

        $this->waitUntilExist($page, 'CRITERIA_OWNER-OCCUPIED_Absentee Owned In-State'); // working javascript CRITERIA_OWNER-OCCUPIED

        $this->session->wait(1);
        $this->scrin('6-1');
        $property = $page->find('named', ['id_or_name', 'PROPERTY_PAGE_IMG']);
        $this->scrin('6-2');
        $property->click();
        $this->session->wait(1);

        $criteria = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->waitUntilDisabled($criteria);
        $this->scrin(7);
        $criteria->selectOption("EQUITY_PCT");

        $this->waitUntilExist($page, 'fromValue_prop'); // working javascript
        $locator_prop = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->waitUntilDisabled($locator_prop); // working javascript

        $this->scrin(8);
        $page->find('named', ['id_or_name', 'fromValue_prop'])->setValue(99);
        $page->find('named', ['id_or_name', 'toValue_prop'])->setValue(100);
        $page->find('named', ['id_or_name', 'addButton_prop'])->click();

        $this->scrin('8-1');
        $this->waitUntilExist($page, 'CRITERIA_EQUITY_PCT_99 to 100 %'); // working javascript

        $locator_prop = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->waitUntilDisabled($locator_prop);
        $locator_prop->selectOption('LAST_SALE_DATE');

        $this->scrin('8-2');
        $this->waitUntilExist($page, 'selBox_LAST_SALE_DATE'); // working javascript
        $this->scrin(9);

        $this->session->executeScript("document.getElementById('last6_LAST_SALE_DATE').click()");
        $this->waitUntilDisabled($locator_prop);
        $this->scrin(10);
        $page->find('named', ['id_or_name', 'prop_add'])->click();

        $dateInputFromValue = $page->find('named', ['id_or_name', 'fromValue_LAST_SALE_DATE'])->getValue();
        $dateInputToValue = $page->find('named', ['id_or_name', 'toValue_LAST_SALE_DATE'])->getValue();
        $this->waitUntilExist($page, 'CRITERIA_LAST_SALE_DATE_'.$dateInputFromValue.'-'.$dateInputToValue); // working javascript
        $this->scrin(11);

        $result['C'] = $this->parceCount($page);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->removeCriteria($page, 'DEL_ID_CRITERIA_EQUITY_PCT_99 to 100 %');
        $this->scrin(12);

        $this->removeCriteria($page, 'DEL_ID_CRITERIA_LAST_SALE_DATE_'.$dateInputFromValue.'-'.$dateInputToValue);
        $this->scrin(13);

        $this->removeCriteria($page, 'DEL_ID_CRITERIA_OWNER-OCCUPIED_Absentee Owned In-State');
        $this->scrin(14);

        $this->removeCriteria($page, 'DEL_ID_CRITERIA_OWNER-OCCUPIED_Absentee Owned Out-of-State');
        $this->scrin(15);

        $criteria = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->waitUntilDisabled($criteria);
        $this->scrin(16);
        $criteria->selectOption("EQUITY_PCT");

        $this->waitUntilExist($page, 'fromValue_prop'); // working javascript
        $locator_prop = $page->find('named', ['id_or_name', 'locator_prop']);
        $this->waitUntilDisabled($locator_prop); // working javascript

        $selectEqu = $page->find('named', ['id_or_name', 'locatorAvailableList_prop']);
        $selectEqu->selectOption('4-4', true);
        $selectEqu->selectOption('5-5', true);
        $selectEqu->selectOption('6-6', true);
        $selectEqu->selectOption('7-7', true);
        $selectEqu->selectOption('8-8', true);
        $selectEqu->selectOption('9-9', true);
        $selectEqu->selectOption('10-10', true);
        $page->find('named', ['id_or_name', 'prop_add'])->click();

        $this->scrin(17);
        $this->waitUntilDisabled($locator_prop);
        $criteria->selectOption("CURRENT_HOME_VALUE");
        $this->waitUntilDisabled($locator_prop);

        $this->scrin('17-1');
        $page->find('named', ['id_or_name', 'fromValue_prop'])->setValue(1);
        $page->find('named', ['id_or_name', 'toValue_prop'])->setValue($result['P']);
        $this->scrin('17-2');
        $page->find('named', ['id_or_name', 'addButton_prop'])->click();
        $this->waitUntilDisabled($locator_prop); // working javascript

        $this->scrin(18);
        $result['R'] = $this->parceCount($page);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $priceSelect = $page->find('named', ['id_or_name', 'locatorSelectedList_prop']);
        $priceSelect->selectOption("1-".$result['P'], true);
        $page->find('named', ['id_or_name', 'prop_remove'])->click();
        $this->waitUntilDisabled($locator_prop);
        $this->scrin(19);

        $newPrice = round($result['P']*0.33);
        $page->find('named', ['id_or_name', 'fromValue_prop'])->setValue(1);
        $page->find('named', ['id_or_name', 'toValue_prop'])->setValue($newPrice);
        $page->find('named', ['id_or_name', 'addButton_prop'])->click();
        $this->waitUntilDisabled($locator_prop); // working javascript
        $this->scrin(20);
        $result['U'] = $this->parceCount($page);

        $priceSelect = $page->find('named', ['id_or_name', 'locatorSelectedList_prop']);
        $priceSelect->selectOption("1-".$newPrice, true);
        $page->find('named', ['id_or_name', 'prop_remove'])->click();
        $this->waitUntilDisabled($locator_prop);
        $this->scrin(21);

        $newPrice1 = $newPrice*2;
        $page->find('named', ['id_or_name', 'fromValue_prop'])->setValue($newPrice);
        $page->find('named', ['id_or_name', 'toValue_prop'])->setValue($newPrice1);
        $page->find('named', ['id_or_name', 'addButton_prop'])->click();
        $this->waitUntilDisabled($locator_prop); // working javascript
        $this->scrin(22);
        $result['V'] = $this->parceCount($page);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->scrin("scrin!!!");
        #VarDumper::dump($result);

        return $result;
        #exit;
        #VarDumper::dump($a->getXpath());
        #VarDumper::dump($a->getOuterHtml());
    }

    private function parceCount($page)
    {
        $result = [];
        $sing1 = $page->find('named', ['id_or_name', 'td_0']);
        if ($sing1) {
            $result[] = $this->getTextValue($sing1);
        }

        $sing2 = $page->find('named', ['id_or_name', 'td_1']);
        if ($sing2) {
            $result[] = $this->getTextValue($sing2);
        }

        $sing3 = $page->find('named', ['id_or_name', 'td_2']);
        if ($sing3) {
            $result[] = $sing3->getText();
        }

        $sing4 = $page->find('named', ['id_or_name', 'td_3']);
        if ($sing4) {
            $result[] = $this->getTextValue($sing4);
        }

        $sing5 = $page->find('named', ['id_or_name', 'td_4']);
        if ($sing5) {
            $result[] = $this->getTextValue($sing5);
        }

        $sing6 = $page->find('named', ['id_or_name', 'td_5']);
        if ($sing6) {
            $result[] = $this->getTextValue($sing6);
        }

        $sing7 = $page->find('named', ['id_or_name', 'td_6']);
        if ($sing7) {
            $result[] = $this->getTextValue($sing7);
        }

        $out = '';
        foreach ($result as $res) {
            if ($res && preg_match("#[0-9]+#", $res)) {
                $out .= $res;
            }
        }

        return $out;
    }

    private function getTextValue($sing)
    {
        if (is_null($sing)) {
            $val = null;
        }
        if ($sing instanceof NodeElement) {
            try {
                $val = $sing->getText();
            } catch (Exception $e) {
                $val = null;
            }

        } else {
            $val = null;
        }
        return $val;
    }
}