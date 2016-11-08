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
    #protected $urlLogin = 'https://www.fmls.com/pub/LoginFail.cfm';
    protected $login = 'rencesmi';
    protected $password = '878526';

    protected $isAuth = false;

    public function parse()
    {
        if ($page = $this->auth()) {
            /** @var NodeElement[] $matrixUrls */
            $matrixUrls = $page->findAll('xpath', '//div[@id="sec-nav"]/ul/li/a');
            $matrixUrls[1]->click(); // click on Matrix
            $this->session->wait(5);

            $names = $this->session->getWindowNames();
            $this->session->switchToWindow($names[1]); //select new window

            $el = $page->find('named', ['content', 'click here']);
            $el->click(); //finish auth

            /** @var NodeElement[] $residentalUrls */
            $residentalUrls = $page->findAll('xpath', '//li/a');
            $residentalUrls[9]->mouseOver(); // hover search
            $residentalUrls[10]->click(); // click on Residental

            /** @var NodeElement[] $checkboxes */
            $checkboxes = $page->findAll('named', ['checkbox', 'Fm45_Ctrl16_LB']);

            foreach ($checkboxes as $checkbox) {
                $text = preg_replace("#\D#", "", explode(" ", $checkbox->getOuterHtml())[3]);
                if (in_array($text, [101, 1027, 1028, 1029, 1031])) {
                    $checkbox->uncheck();
                }
                if ($text == 104) {
                    $checkbox->check();
                    $soldInput = $page->find('named', ['id_or_name', 'FmFm45_Ctrl16_104_Ctrl16_TB']);
                    $soldInput->setValue("0-180");
                }
            }

            $zipCodeInput = $page->find('named', ['id_or_name', 'Fm45_Ctrl68_TextBox']);
            $zipCodeInput->setValue($this->zipCode);

            $page->clickLink('m_ucSearchButtons_m_lbSearch');

            $stats = null;
            while (empty($stats)) {
                $stats = $page->find('named', ['id_or_name', 'm_btnStats']);
                $this->session->wait(1);
            }

            $page->clickLink('m_btnStats');
            $page->clickLink('m_lbOldStats');

            $price = $dom = null;
            $tds = $page->findAll('xpath', '//td');
            foreach ($tds as $key => $td) {
                if ($key == 70) {
                    $price = $td->getText(); #median price
                    $price = preg_replace("#[^0-9]#", "", $price);
                }
                if ($key == 65) {
                    $dom = $td->getText(); #avg dom
                }
            }
            return ['p' => $price, 'n' => $dom];
        } else {
            throw new \Exception('Authorizing false');
        }

    }

    protected function auth()
    {
        $this->createSession();
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

        $loginButtons = $registerForm->findAll('xpath', "//input");
        $loginButton = array_pop($loginButtons);
        $loginButton->click();

        $el = $page->find('named', ['content', 'Logout']);

        if ($el instanceof NodeElement) {
            return $page;
        } else {
            return false;
        }
    }
}