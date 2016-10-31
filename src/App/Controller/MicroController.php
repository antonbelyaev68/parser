<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ParserMlsmatrix;

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
        //var_dump(1); exit;
        /** @var ParserMlsmatrix $parserMlsmatrix */
        $parserMlsmatrix = $this->get("parser.mlsmatrix");
        $parserMlsmatrix->setZipCodes($request->get('zip_codes'));
        $parserMlsmatrix->parse();

//        $parserListsource = $this->get("parser.listsource");
//        $parserListsource->setZipCodes($request->get('zip_codes'));
//        $parserListsource->parse();

        exit;
    }
    
}