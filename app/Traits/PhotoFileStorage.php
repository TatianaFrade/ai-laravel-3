<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait PhotoFileStorage
{
    public function storePhoto(?UploadedFile $uploadedFile, Model $model, string $field = 'photo_url', string $folder = 'photos'): ?string
    {
        if ($uploadedFile) {
            $path = basename(Storage::disk('public')->putFile($folder, $uploadedFile));
            $model->$field = $path;
            $model->save();
            return $path;
        }
        return null;
    }

    public function deletePhoto(Model $model, string $field = 'photo_url', string $folder = 'photos'): bool
    {
        $photo = $model->$field;

        if ($photo) {
            if (Storage::disk('public')->exists("$folder/$photo")) {
                Storage::disk('public')->delete("$folder/$photo");
            }
            $model->$field = null;
            $model->save();
            return true;
        }
        return false;
    }

    public function deletePhotoFile(?string $photo_url, string $folder = 'photos'): bool
    {
        if ($photo_url && Storage::disk('public')->exists("$folder/$photo_url")) {
            Storage::disk('public')->delete("$folder/$photo_url");
            return true;
        }
        return false;
    }
}
