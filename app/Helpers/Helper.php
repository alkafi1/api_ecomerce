<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class Helper
{
    public static function ImageUploadAndDelete($new_image, $old_image, $directory)
    {
        // Check if a new image is being uploaded
        if ($new_image) {
            
            // Delete the old image if it exists
            if ($old_image && file_exists(public_path($directory . '/' . $old_image))) {
                unlink(public_path($directory . '/' . $old_image));
            }
            // Store the new image
            $image = $new_image;
            $imageName = uniqid() . '_' .$image->getClientOriginalName();
            $image->move(public_path($directory), $imageName);
            return $imageName;
        }

        return null;
    }

    public static function imageUpload(UploadedFile $image, string $directory)
    {
        // Generate a unique filename for the image
        $imageName = uniqid() . '_' . $image->getClientOriginalName();

        // Store the image
        $image->move(public_path($directory), $imageName);

        return $imageName;
    }
}
