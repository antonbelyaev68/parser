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

class ParserMlsmatrix extends Parser
{
    protected $url = '';
    protected $urlLogin = 'https://www.fmls.com/MemberLogin/login.cfm';
    protected $login = 'rencesmi';
    protected $password = '878526';

    protected $isAuth = false;

    public function parse()
    {
        if ($page = $this->auth()) {
            /** @var NodeElement[] $matrixUrls */
            $matrixUrls = $page->findAll('xpath', '//div[@id="sec-nav"]/ul/li/a');
            $matrixUrls[1]->click(); // click on Matrix


            $page = $this->session->getPage();
            $el = $page->find('named', ['content', 'click here']);
            $el->rightClick();

//            $page = $this->session->getPage();
//            $submitForm = $page->find('named', ['id_or_name', 'samlform']);
//            $submitForm->submit();
            VarDumper::dump($page->getHtml()); exit;

            $page = $this->session->getPage();
            /** @var NodeElement[] $residentalUrls */
            $residentalUrls = $page->findAll('xpath', '//li/a');
            $residentalUrls[10]->click(); // click on Residental

            $page = $this->session->getPage();
            /** @var NodeElement[] $checkboxes */
            $checkboxes = $page->findAll('named', ['checkbox', 'Fm45_Ctrl16_LB']);

            foreach ($checkboxes as $checkbox) {
                $text = preg_replace("#\D#", "", explode(" ", $checkbox->getOuterHtml())[3]);
//                VarDumper::dump($text);
//                VarDumper::dump($checkbox->getOuterHtml());
                if (in_array($text, [101, 1027, 1028, 1029, 1031])) {
                    //$checkbox->click();
                    $checkbox->uncheck();
//                    VarDumper::dump("uncheck");
                }
                if ($text == 104) {
                    $checkbox->check();
                    VarDumper::dump("check");
                    $soldInput = $page->find('named', ['id_or_name', 'FmFm45_Ctrl16_104_Ctrl16_TB']);
                    $soldInput->setValue("0-180");
                }
            }

            $zipCodeInput = $page->find('named', ['id_or_name', 'Fm45_Ctrl68_TextBox']);
            $zipCodeInput->setValue("30236");

            $this->session->executeScript('__doPostBack(\'m_ucSearchButtons$m_lbSearch\',\'\')');
//            $submitLink = $page->find('named', ['id_or_name', 'm_ucSearchButtons_m_lbSearch']);
//            $submitLink->click();

            $page = $this->session->getPage();
            VarDumper::dump($page->getHtml()); exit;
            #var_dump($page->getHtml());

        } else {
            throw new \Exception('Authorizing false');
        }

        exit;
    }

    protected function auth()
    {
        $driver = new Selenium2Driver('chrome', null, 'http://192.168.99.100:4444/wd/hub');
//        $this->session = new Session(new GoutteDriver());
        $this->session = new Session($driver);
        $this->session->start();
        sleep(10);

        $this->session->visit($this->urlLogin);
        $page = $this->session->getPage();

        $registerForm = $page->find('named', ['id_or_name', 'FMLSLogin']);
        if (null === $registerForm) {
            throw new \Exception('The element is not found');
        }

        $loginField = $registerForm->findField('UserName');
        $passwordField = $registerForm->findField('Password');
        $loginField->setValue($this->login);
        $passwordField->setValue($this->password);

//        $loginButtons = $registerForm->findAll('xpath', "//input");
//        $loginButton = array_pop($loginButtons);
//        $loginButton->rightClick();
//        VarDumper::dump(get_class($loginButton)); exit;

        $registerForm->submit();

        $page = $this->session->getPage();
//        $el = $page->find('named', ['content', 'Logout']);

        return $page;
//        if ($el instanceof NodeElement) {
//            return $page;
//        } else {
//            return false;
//        }
    }
}