<?php
namespace App\Utils;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class PDFGenerator
{
    public const DEFAULT_FOLDER='reports';

    /**
     * generate pdf
     * @param string $html
     * @param string $orientation
     * @param string $paper
     * @param string|null $path
     */

     public function generate(string $html, string $orientation='portrait', string $paper='A4')
     {
        $folderPath=public_path().'/'.self::DEFAULT_FOLDER;
            
        if(!File::isDirectory($folderPath))
        {
        $this->create_folder($folderPath);
        $this->write_git_ignore($folderPath);
        }
        $filename=self::DEFAULT_FOLDER. '/'.md5(Str::uuid()). '.pdf';
        $path= public_path().'/'.$filename;
        $url= config('app.url').'/'.$filename;
        $pdf= Pdf::loadHTML($html)->setPaper($paper, $orientation)->setWarnings(false);
  $pdf->save($path);
  return $url;
     }


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