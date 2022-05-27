<?php 
    /**
     * Saves an image coming from $_FILES
     * 
     * @param array     $file         The file from $_FILES
     * @param string    $path         The folder in /assets/pictures where this image will be saved
     * @param int       $id           The name to give the image
     * @param int       $size         The biggest size the longest side of the resulting image can have
     * @param float|int $aspect_ratio The aspect ratio the resulting image will have
     * 
     * @return false if there was an error
     * @return true  if it was successful
     */
    function uploadImage(array $file, string $path, int $id, int $size, float|int $aspect_ratio = 0): bool {
        if ($file['error'])
            return false;

        $image_path = dirname(__DIR__)."/assets/pictures/$path/$id.webp";

        unlink($image_path); // no biggie if it fails

        $image = imagecreatefromstring(file_get_contents($file['tmp_name']));

        if ($image === false) return false;

        $original_width = imagesx($image);     // width of the original image
        $original_height = imagesy($image);    // height of the original image
        $original_aspect_ratio = $original_width / $original_height;

        if ($aspect_ratio <= 0)
            $aspect_ratio = $original_aspect_ratio;

        if ($aspect_ratio > 1) {
            $dest_width = min($size, $original_width);
            $dest_height = $dest_width / $aspect_ratio;
        } else {
            $dest_height = min($size, $original_height);
            $dest_width = $dest_height * $aspect_ratio;
        }

        if ($original_aspect_ratio > $aspect_ratio) {
            $src_width = $original_height*$aspect_ratio;
            $src_height = $original_height;
            $offset_x = ($original_width - $src_width) / 2;
            $offset_y = 0;
        } else {
            $src_width = $original_width;
            $src_height = $original_width/$aspect_ratio;
            $offset_x = 0;
            $offset_y = ($original_height - $src_height) / 2;
        }

        $resized = imagecreatetruecolor($dest_width, $dest_height);
        imagecopyresized($resized, $image, 0, 0, $offset_x, $offset_y, $dest_width, $dest_height, $src_width, $src_height);

        return imagewebp($resized, $image_path);
    }
?>