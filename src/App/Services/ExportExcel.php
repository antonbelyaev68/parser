<?php

namespace App\Services;

class ExportExcel
{
    public function export($data)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $rowNum = 2;
        foreach ($data as $code => $row) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNum, $code);
            foreach ($row as $letter => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue($letter.$rowNum, $value);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('info');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="info.xls"');
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}