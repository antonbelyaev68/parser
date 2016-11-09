<?php

namespace App\Services;

class ExportExcel
{
    public function export($data)
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load("template.xlsx");

        $objPHPExcel->setActiveSheetIndex(0);

        $rowNum = 2;
        foreach ($data as $code => $row) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNum, $code);
            foreach ($row as $letter => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue($letter.$rowNum, $value);
            }
            $rowNum++;
        }

        //$objPHPExcel->getActiveSheet()->setTitle('info');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Marketing_Chart"');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}