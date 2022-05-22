<?php 
    function uploadProfilePicture(array $file, int $userId): bool {

        $imagePath = sprintf('%s/assets/pictures/profile/%d.jpg', dirname(__DIR__), $userId);

        unlink($imagePath); // no biggie if it fails

        return move_uploaded_file($file['tmp_name'], $imagePath);
    }
?>