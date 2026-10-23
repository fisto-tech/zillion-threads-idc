<?php
// Split the reference image (Media 2: 673 x 419, which has black line in center) into page4-target.png and page5-target.png
$srcPath = 'C:/Users/FISH EYE/.gemini/antigravity-ide/brain/01c4c768-db04-44c7-b5b7-7d22fa2c6586/.user_uploaded/media_1792759666540.png';
$src = imagecreatefrompng($srcPath);
$w = imagesx($src);
$h = imagesy($src);

// Find the center spine / black line
// Let's inspect x around $w/2 = 336
echo "Total size: $w x $h\n";

// In a 2-page spread, left page is page 4, right page is page 5.
// Let's create page4-target.png (0 to 333) and page5-target.png (338 to 673)
$spineX = 335; // approximate center spine
$halfW = floor($w / 2);

// Page 4: 0, 0, $halfW, $h
$p4 = imagecreatetruecolor($halfW, $h);
imagecopy($p4, $src, 0, 0, 0, 0, $halfW, $h);
$outDir = __DIR__ . '/../global assets/Images/targets/';
if (!is_dir($outDir)) mkdir($outDir, 0777, true);

imagepng($p4, $outDir . 'page4-target.png');

// Page 5: $halfW, 0, $halfW, $h
$p5 = imagecreatetruecolor($w - $halfW, $h);
imagecopy($p5, $src, 0, 0, $halfW, 0, $w - $halfW, $h);
imagepng($p5, $outDir . 'page5-target.png');

echo "Saved page4-target.png ({$halfW}x{$h}) and page5-target.png (" . ($w - $halfW) . "x{$h}) to {$outDir}\n";

// Also check aspect ratio: 4/5 means width/height = 0.8
echo "Current target aspect ratio: " . ($halfW / $h) . " (vs 4/5 = " . (4/5) . ")\n";
