<?php

namespace App\PhotoDelete;

use Illuminate\Support\Facades\File;

trait HasImageDeleting
{
    protected static function bootHasImageDeleting()
    {
        static::deleting(function ($model) {
            foreach ($model->getImageFields() as $field) {
                $filePath = $model->$field;

                if (!empty($filePath)) {
                    $fullPath = public_path('uploads/' . $filePath);

                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                }
            }
        });

        static::updating(function ($model) {
            foreach ($model->getImageFields() as $field) {
                if ($model->isDirty($field)) {
                    $original = $model->getOriginal($field);

                    if (!empty($original)) {
                        $fullPath = public_path('uploads/' . $original);

                        if (File::exists($fullPath)) {
                            File::delete($fullPath);
                        }
                    }
                }
            }
        });
    }

    protected function getImageFields(): array
    {
        return property_exists($this, 'imageFields') ? $this->imageFields : ['img'];
    }
}
