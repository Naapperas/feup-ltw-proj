<?php 
    function uploadProfilePicture(array $file, int $userId): bool {

        $imagePath = sprintf('%s/assets/pictures/profile/%d.webp', dirname(__DIR__), $userId);

        unlink($imagePath); // no biggie if it fails

        $image = imagecreatefromstring(file_get_contents($file['tmp_name']));

        if ($image === false) return false;

        $width = imagesx($image);     // width of the original image
        $height = imagesy($image);    // height of the original image
        $square = min($width, $height);  // size length of the maximum square

        $destWidth = 320;
        $destHeight = 320;

        $resized = imagecreatetruecolor($destWidth, $destHeight);
        imagecopyresized($resized, $image, 0, 0, ($width>$square)?($width-$square)/2:0, ($height>$square)?($height-$square)/2:0, $destWidth, $destHeight, $square, $square);

        return imagewebp($resized, $imagePath);
    }
?>