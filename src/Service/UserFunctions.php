<?php

namespace App\Service;

class UserFunctions
{

    public function createAvatarFile($username) {
        $file = 'img/users/default.png';
        $newfile = 'img/users/'.$username.'.png';
        if (!copy($file, $newfile)) {
            echo "<p class='failed'>Failed to create new user avatar\n</p>";
        }
        else {
            return $newfile;
        }
    }

    public function changeAvatar($file,$user) {
        /*$results = "";
        $line = $results->fetch();
        $tmp_name = $_FILES['newAvatar']['tmp_name'];*/
        $filename = $user->getUsername().'.png';
        $upload = move_uploaded_file($file, "./img/users/$filename");
        if(!$upload) {
            echo '<p class="failed">Upload failed</p>';
            echo $_FILES['newAvatar']['error'];
        }
        else {
            ?>
            <p class="success">Profile picture updated !</p>
            <p>Refresh your profile page to see changes</p>
            <?php
        }
    }

}
?>