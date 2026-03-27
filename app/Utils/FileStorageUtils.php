<?php

namespace App\Utils;

use App\Exports\ExcelExport;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class FileStorageUtils
{


    /**
     * store and generate profile image url
     *
     * @return string!null
     */
    public static function store_profile_img(string $event, string $type, $img): ?string
    {
        if ($img) {
            // $voteImgPath=Storage::disk('neutek_vote')->put( Str::uuid() . "." . $img->getClientOriginalExtension(), $img);
            // Storage::disk('neutek_vote')->url($voteImgPath);
            // $path = $img->storeAs('public/users/profile', Str::uuid() . "." . $img->getClientOriginalExtension());
            $path = Storage::disk('public')->put($event . '/' . $type . '/' . Str::uuid() . "." . $img->getClientOriginalExtension(), $img);
            $url = Storage::disk('public')->url($path);
            self::write_git_ignore();
            return $url;
        }
        return null;
    }


    /**
     * uploading a file to storage
     *
     * @param UploadedFile $file
     * @return array
     */
    final public static function documentUpload(UploadedFile $file, $bool=true): object
    {
        try {

            if(!$bool)
            {
                 return (object) [
                    'success' => true,
                    'uri' => "",
                    'encoded' => ""
                ];
            }

            $name = Uuid::uuid4()->toString() . "." . $file->getClientOriginalExtension();
            $filePath = "public/documents/$name";

            $baseUrl = config('app.url');
            $fileUrl = $baseUrl . '/storage/documents/' . $name;
            Storage::put($filePath, file_get_contents($file));


            return (object) [
                'success' => true,
                'uri' => $fileUrl,
                'encoded' => base64_encode(file_get_contents($file))
            ];
        } catch (Exception $ex) {
            Log::error(__CLASS__, [$ex]);

            return (object) [
                'success' => false,
                'message' => $ex->getMessage(),
            ];
        }
    }

    //TODO: WRITE GIT IGNORE
    public static function write_git_ignore(): void
    {
        $fileName = 'public/.gitignore';
        if (is_file($fileName)) {
            $content = "*\n!.gitignore";
            file_put_contents($fileName, $content);
        }

    }


    public static function storeBase64File($location,$image_64)
    {
         //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
      
        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
      
      // find substring fro replace here eg: data:image/png;base64,
      
       $image = str_replace($replace, '', $image_64); 
      
       $image = str_replace(' ', '+', $image); 
      
       $imageName = Str::random(10).'.'.$extension;

       $imgName = "documents/$location$imageName";

       $baseUrl = config('app.url');
       $fileUrl = "$baseUrl/storage/documents/$location$imageName";
      
       Storage::disk('public')->put($imgName, base64_decode($image));
       return $fileUrl;
    }
}