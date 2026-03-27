<?php

namespace App\Utils;

use App\Exports\ExcelExport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExcelGenerator
{

    public const DEFAULT_FOLDER='reports';


/**
 * generate excel
 * @param collection $header
 * @param collection $data
 * @param array $fileInfo
 */

 public function generate($header, $rows, $fileInfo)
 {
    $folderPath=public_path().'/'.self::DEFAULT_FOLDER;
    if(!File::isDirectory($folderPath))
    {
$this->create_folder($folderPath);
$this->write_git_ignore($folderPath);
    }
    $fileName=self::DEFAULT_FOLDER.'/'.md5(Str::uuid()).'.xlsx';
    $url=config('app.url').'/'.$fileName;
    $excel= new ExcelExport($header, $rows, $fileInfo);
    Excel::store($excel,  $fileName,'public_uploads');
    return  $url;}

 //TODO: WRITE GIT IGNORE
 public function write_git_ignore( string $folderPath):void
{
    $fileName=$folderPath. '/.gitignore';
    if(is_file($fileName))
    {
        $content="*\n!.gitignore";
        file_put_contents($fileName, $content);
    }
    
}

 //TODO: CREATE FOLDER
 public function create_folder( string $folderPath)
 {
    File::makeDirectory($folderPath);
 }
}