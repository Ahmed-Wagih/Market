<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\File;
use Illuminate\Support\Facades\Storage;

trait Upload
{
     public static function upload($data=[]){

        $new_name = $data['new_name'] === null ? time() : $data['new_name'];

        if(request()->hasFile($data['file']) && $data['upload_type'] == 'single'){
            !empty($data['old_image']) ? Storage::delete($data['old_image']) : '';
            return request()->file($data['file'])->store($data['path']);
        }
     }
}
