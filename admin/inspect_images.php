<?php
$f1 = 'C:/Users/FISH EYE/.gemini/antigravity-ide/brain/01c4c768-db04-44c7-b5b7-7d22fa2c6586/.user_uploaded/media_1792759660063.png';
$f2 = 'C:/Users/FISH EYE/.gemini/antigravity-ide/brain/01c4c768-db04-44c7-b5b7-7d22fa2c6586/.user_uploaded/media_1792759666540.png';

$s1 = getimagesize($f1);
$s2 = getimagesize($f2);

echo "Media 1: {$s1[0]} x {$s1[1]}\n";
echo "Media 2: {$s2[0]} x {$s2[1]}\n";

// Let's create target images if needed
// Notice media 2 is 482x300 or similar spread?
// Let's see if media 1 or 2 is spread or separate pages
