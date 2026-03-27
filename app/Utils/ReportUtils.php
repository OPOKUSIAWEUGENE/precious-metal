<?php

namespace App\Utils;

use App\Utils\ExcelGenerator;
use App\Utils\PDFGenerator;

class ReportUtils
{

    const EXCEL="excel";
    const PDF="pdf";

    /**
 * parse the html for pdf
 * 
 * @param $data
 * @param $title
 * @return string
 */

 final protected static function parseReportHtml($headers, $data, $fileInfo): string
 {

    $html= '<html>
            <head>
            <style>
            table{
            border-collapse: collapse;
            width:100%;
            font-size:12px;
           
            }
          
          
            th, td{
             
            text-align:left;
            padding:8px;
            border:1px solid #C0C0C0;
            color:black;
            }
            tr:nth-child(even){
                backgrounnd-color:#f2f2f2;
            }
            th{
                border-bottom:1px solid black;
               }
            td{
                color:#273746;
            }
            .heading{
                font-size:12px;
                width:100%;
                color:rgb(199, 0, 57, 0.9);
                text-align:center;
            }
            </style>
            </head>
            <body>';

           $heading='<div class="heading" >'.$fileInfo['filetitle'] ;
           $heading.='<br>'.$fileInfo['filename'];
           $heading.='<br>'.$fileInfo['title'];
           $heading.='<br>'.$fileInfo['description'].'</div>';

        $table='<table>';
        $thead='<thead><tr>';
        $header='';
      

        foreach($headers as $title){

            $header.='<th>'.$title.'</th>';

        }
            $thead=$thead .$header. '</tr></thead>';
            $tbody='<tbody>';
            $row='';
          
    $data->each(function ($item) use (&$row){

            $row.="<tr>";
    foreach($item as $value)
    {
            $row.= '<td>'.($value?$value:'-').'</td>';
    }
            $row.='</tr>';
    });

            $tbody=$tbody .$row. '</tbody>';
            $table= $table .$thead.$tbody. '</table>';

            return $html.$heading.$table.'</body></html>';
 }

//TODO: GENERATE PDF
 protected static function generatePDF($header, $data, $fileInfo)
 {
    $pdf= new PDFGenerator();
    $html= self::parseReportHtml($header, $data, $fileInfo);
    return $pdf->generate($html, 'landscape', 'A4');
 }

 //TODO: GENERATE EXCEL
 protected static function generateExcel($header, $data, $fileInfo)
 {
    $excel= new ExcelGenerator();
    return $excel->generate($header, $data, $fileInfo);
 }

public static function generateReportFile($header, $rows, $fileInfo, $type)
{
    if($type==self::PDF)
    {
        return self::generatePDF($header, $rows, $fileInfo);

    }else if($type==self::EXCEL)
    {
        return self::generateExcel($header, $rows, $fileInfo);
    }
}

    
}