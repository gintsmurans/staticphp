<?php

namespace System\Modules\Presentation\Models\Tables\Export;

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;

use \System\Modules\Presentation\Models\Tables\Table;

class Excel extends Table
{
    public function generate(): Spreadsheet
    {
        $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $active_sheet =  $xls->setActiveSheetIndex(0);

        $colNr = 1;
        $rowNr = 1;
        foreach ($this->columns as $column) {
            if (empty($column['export_key'])) {
                continue;
            }

            $active_sheet->getCellByColumnAndRow($colNr, $rowNr)->setValue($column['title']);
            $colNr += 1;
        }

        $rowNr = 2;
        foreach ($this->rows as $rowData) {
            $colNr = 1;
            foreach ($this->columns as $column) {
                if (empty($column['export_key'])) {
                    continue;
                }
                $exportKey = $column['export_key'];
                $cellValue = is_callable($exportKey) ? $exportKey($rowData) : $rowData[$exportKey];
                $cell = $active_sheet->getCellByColumnAndRow($colNr, $rowNr);

                switch ($column['type']) {
                    case 'int':
                    case 'float':
                        $cell->setValueExplicit($cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    break;

                    default:
                        $cell->setValueExplicit($cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    break;
                }
                $colNr += 1;
            }
            $rowNr += 1;
        }

        return $xls;
    }

    public function output($filename, $author = '', $formatter = null)
    {
        $xls = $this->generate();
        $xls->getProperties()
            ->setCreator($author)
            ->setLastModifiedBy($author);

        // Format xls
        if (is_callable($formatter)) {
            $formatter($xls);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = IOFactory::createWriter($xls, 'Xlsx');
        $objWriter->setPreCalculateFormulas(false);
        $objWriter->save('php://output');
    }
}
