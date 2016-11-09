<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ParserMlsmatrix;
use App\Services\ParserListsource;
use App\Services\ExportExcel;

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
        /** @var ParserMlsmatrix $parserMlsmatrix */
        $parserMlsmatrix = $this->get("parser.mlsmatrix");
        /** @var ParserListsource $parserListsource */
        $parserListsource = $this->get("parser.listsource");
        /** @var ExportExcel $exportExcel */
        $exportExcel = $this->get("export.excel");

        $codes = $request->get('zip_codes');
        $codes = str_replace(" ", "", $codes);
        $codes = explode(",", $codes);

        $data = [];

        foreach ($codes as $code) {
            $parserMlsmatrix->setZipCode($code);
            $matrixData = $parserMlsmatrix->parse();

            $parserListsource->setZipCode($code);
            $parserListsource->setMatrixResult($matrixData);
            $data[$code] = $parserListsource->parse();
        }

        $exportExcel->export($data);

        exit;
    }
    
}