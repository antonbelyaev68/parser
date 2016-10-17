<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MicroController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return new Response(
            '<html>
            <body>
                <form action="/parse" method="POST">
                    <input type="text" name="zip_codes" size="50">
                    <input type="submit" value="Start parsing">
                </form>
            </body>
            </html>'
        );
    }
    
    /**
     * @Route("/parse")
     */
    public function parseAction(Request $request)
    {
        var_dump($request->get('zip_codes'));
        exit;
    }
    
}