<?php

function getPreview($imgFile, $maxSize = THUMB_SIZE)
{
        # example: data/myalbum/100.mypic.jpg
        $newImgFile = DATA_DIR."/".dirname($imgFile)."/".$maxSize.".".basename($imgFile);

        # if the preview is a symlink, image is already good sized
        if (is_link($newImgFile)) return $imgFile;

        if (! is_file($newImgFile))
        {
                # this tels the template to flush output after displaying previews
                $GLOBALS["generating"] = true;

                # reset script time limit to 20s (wont work in safe mode)
                set_time_limit(20);

                $ext = strtolower(substr($imgFile, -4));
                if ($ext == ".jpg")
                        $img = imagecreatefromjpeg($imgFile);
                else
                        $img = imagecreatefrompng($imgFile);

                $w = imagesx($img);
                $h = imagesy($img);
                # if the image is already small, make a symlink, and return it
                if ($w <= $maxSize and $h <= $maxSize) {
                        imagedestroy($img);
                        symlink($imgFile, $newImgFile);
                        return $imgFile;
                }

                # config to allow group writable files
                umask(DATA_UMASK);
                # create the thumbs directory recursively
                if (! is_dir(dirname($newImgFile))) mkdir(dirname($newImgFile), 0777, true);

                //if ($w > $h) {
                //      $newW = $maxSize;
                //      $newH = $h/($w/$maxSize);
                //} else {
                        $newW = $w/($h/$maxSize);
                        $newH = $maxSize;
                //}

                $newImg = imagecreatetruecolor($newW, $newH);

                imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

                if ($ext == ".jpg")
                        imagejpeg($newImg, $newImgFile);
                else
                        imagepng($newImg, $newImgFile);

                imagedestroy($img);
                imagedestroy($newImg);
        }

        return $newImgFile;
}


?>
