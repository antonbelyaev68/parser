<?php

namespace App\Services;

use App\Services\Parser;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Goutte\Client as GoutteClient;
use Behat\Mink\Element\NodeElement;

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
            
        } else {
            throw new \Exception('Authorizing false');
        }


        exit;
    }

    protected function auth()
    {
        $this->session = new Session(new GoutteDriver());
        $this->session->start();

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
        $registerForm->submit();

        $page = $this->session->getPage();
        $el = $page->find('named', ['content', 'Logout']);

        if ($el instanceof NodeElement) {
            return $page;
        } else {
            return false;
        }
    }
}