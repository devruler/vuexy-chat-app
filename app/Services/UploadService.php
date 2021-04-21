<?php

namespace App\Services;

class UploadService
{

    public static function upload($file, ?object $attachTo = null)
    {
        $file = $file;
        $ext = $file->extension();
        $name = uniqid() . '.' . $ext;
        $path = '/storage/attachments/' .  $name;
        $file->storePubliclyAs('public', '/attachments/' . $name);

        if($attachTo){
            $attachTo->attachment()->create([
                'name' => $name,
                'path' => $path,
                'extension' => $ext,
            ]);
        }

    }
}
