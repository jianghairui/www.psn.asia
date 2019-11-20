<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/6/17
 * Time: 15:03
 */
namespace app\admin\controller;
use think\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Test extends Controller {

    public function index() {
        $list = [
            [
                'name'=>'Jiang',
                'age' => 28,
                'id' => 1
            ],
            [
                'name'=>'Hanchi',
                'age' => 28,
                'id' => 2
            ]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('纸巾机统计');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);


        $sheet->getStyle('A')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

        $sheet->mergeCells('A1:C1');

        $sheet->setCellValue('A1', '纸巾机销售统计' . date('Y-m-d H:i:s') . ' 制表人:' . session('username'));
        $sheet->setCellValue('A2', '设备名');
        $sheet->setCellValue('B2', '设备号');
        $sheet->setCellValue('C2', '销售额(元)');
        $sheet->getStyle('A2:C2')->getFont()->setBold(true);

        $index = 3;
        foreach ($list as $v) {
            $sheet->setCellValue('A'.$index, $v['name']);
            $sheet->setCellValue('B'.$index, $v['age']);
            $sheet->setCellValue('C'.$index, $v['id']);
            $index++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
//header(‘Content-Type:application/vnd.ms-excel‘);//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="demo'.date('Y-m-d').'.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }

    public function test()
    {
        // 有Xls和Xlsx格式两种
        $objReader = IOFactory::createReader('Xlsx');

        $filename = ROOT_PATH . '/demo.xlsx';
        $objPHPExcel = $objReader->load($filename);  //$filename可以是上传的表格，或者是指定的表格
        $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
        $highestRow = $sheet->getHighestRow();       // 取得总行数

        //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
        for ($j = 3; $j <= $highestRow; $j++) {
            $data[] = [
                'name' => $sheet->getCell("A" . $j)->getValue(),
                'age' => $sheet->getCell("B" . $j)->getValue(),
                'money' => $sheet->getCell("C" . $j)->getValue()
            ];
        }
        halt($data);
    }

}