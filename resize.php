<?php
$src = imagecreatefrompng('public/favicon.png');
$dst = imagecreatetruecolor(64, 64);
imagealphablending($dst, false);
imagesavealpha($dst, true);
$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
imagefilledrectangle($dst, 0, 0, 64, 64, $transparent);
imagecopyresampled($dst, $src, 0, 0, 0, 0, 64, 64, 1024, 1024);
imagepng($dst, 'public/favicon-64x64.png');
imagedestroy($src);
imagedestroy($dst);
echo "Done";
