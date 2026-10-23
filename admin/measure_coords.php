<?php
// Exact measurement calculation tool for Page 4 & Page 5
// Page 4: Width = 333, Height = 419
// Page 5: Width = 337, Height = 419

$p4W = 333.0;
$p4H = 419.0;

$p5W = 337.0;
$p5H = 419.0;

function m4($x, $y, $w, $h) {
    global $p4W, $p4H;
    return [
        'x' => round(($x / $p4W) * 100, 2),
        'y' => round(($y / $p4H) * 100, 2),
        'w' => round(($w / $p4W) * 100, 2),
        'h' => round(($h / $p4H) * 100, 2),
    ];
}

function m5($x, $y, $w, $h) {
    global $p5W, $p5H;
    return [
        'x' => round(($x / $p5W) * 100, 2),
        'y' => round(($y / $p5H) * 100, 2),
        'w' => round(($w / $p5W) * 100, 2),
        'h' => round(($h / $p5H) * 100, 2),
    ];
}

/*
PAGE 4:
Logo & Subtitle:
y: 18 to 48 -> y: 4.3%, h: 7.2%
Header text: Y A R I K A Collections

Section 1: Twirly (top-left)
Heading: x: 24, y: 70, w: 112, h: 12 -> 7.2%, 16.7%, 33.6%, 2.9%
- p4-twirly-1 (arch model left):
  x: 13, y: 84, w: 68, h: 93 -> 3.9%, 20.0%, 20.4%, 22.2% (radius: 999px 999px 0 0)
- p4-twirly-2 (rectangle model right):
  x: 84, y: 84, w: 70, h: 93 -> 25.2%, 20.0%, 21.0%, 22.2% (radius: 4px)
- p4-twirly-3 (detail swatch left):
  x: 11, y: 180, w: 38, h: 41 -> 3.3%, 43.0%, 11.4%, 9.8% (radius: 2px)
- p4-twirly-4 (detail swatch center-left):
  x: 51, y: 180, w: 32, h: 41 -> 15.3%, 43.0%, 9.6%, 9.8% (radius: 2px)
- p4-twirly-5 (detail swatch center-right):
  x: 85, y: 180, w: 33, h: 41 -> 25.5%, 43.0%, 9.9%, 9.8% (radius: 2px)
- p4-twirly-6 (detail swatch right):
  x: 120, y: 180, w: 34, h: 41 -> 36.0%, 43.0%, 10.2%, 9.8% (radius: 2px)

Section 2: Edge Crop (top-right)
Heading: x: 171, y: 70, w: 123, h: 12 -> 51.4%, 16.7%, 36.9%, 2.9%
- p4-edge-1 (red arch model left):
  x: 167, y: 88, w: 46, h: 132 -> 50.2%, 21.0%, 13.8%, 31.5% (radius: 999px 999px 0 0)
- p4-edge-2 (red sleeve top swatch):
  x: 217, y: 88, w: 42, h: 40 -> 65.2%, 21.0%, 12.6%, 9.5% (radius: 2px)
- p4-edge-3 (red texture mid swatch):
  x: 217, y: 131, w: 42, h: 44 -> 65.2%, 31.3%, 12.6%, 10.5% (radius: 2px)
- p4-edge-4 (black sleeve bot swatch):
  x: 217, y: 178, w: 42, h: 40 -> 65.2%, 42.5%, 12.6%, 9.5% (radius: 2px)
- p4-edge-5 (black sleeve top right swatch):
  x: 263, y: 88, w: 47, h: 36 -> 79.0%, 21.0%, 14.1%, 8.6% (radius: 2px)
- p4-edge-6 (black model right):
  x: 263, y: 127, w: 47, h: 93 -> 79.0%, 30.3%, 14.1%, 22.2% (radius: 4px)

Section 3: Wave (bottom-left)
Heading: x: 25, y: 236, w: 108, h: 12 -> 7.5%, 56.3%, 32.4%, 2.9%
- p4-wave-1 (necklace detail top):
  x: 12, y: 252, w: 90, h: 44 -> 3.6%, 60.1%, 27.0%, 10.5% (radius: 3px)
- p4-wave-2 (leaf print mid swatch):
  x: 12, y: 300, w: 45, h: 44 -> 3.6%, 71.6%, 13.5%, 10.5% (radius: 2px)
- p4-wave-3 (blue dress girl sitting left):
  x: 22, y: 330, w: 47, h: 68 -> 6.6%, 78.8%, 14.1%, 16.2% (radius: 4px, z: 2)
- p4-wave-4 (brown sleeve detail):
  x: 60, y: 300, w: 42, h: 63 -> 18.0%, 71.6%, 12.6%, 15.0% (radius: 2px)
- p4-wave-5 (brown neck detail bottom):
  x: 71, y: 366, w: 64, h: 32 -> 21.3%, 87.4%, 19.2%, 7.6% (radius: 3px, z: 2)
- p4-wave-6 (brown dress girl sitting right):
  x: 105, y: 252, w: 45, h: 110 -> 31.5%, 60.1%, 13.5%, 26.3% (radius: 4px)

Section 4: Muse (bottom-right)
Heading: x: 198, y: 236, w: 108, h: 12 -> 59.5%, 56.3%, 32.4%, 2.9%
- p4-muse-1 (kurti pocket swatch left):
  x: 161, y: 315, w: 38, h: 56 -> 48.4%, 75.2%, 11.4%, 13.4% (radius: 3px, z: 2)
- p4-muse-2 (standing girl in peach/orange kurti arch):
  x: 175, y: 254, w: 66, h: 144 -> 52.6%, 60.6%, 19.8%, 34.4% (radius: 999px 999px 0 0, z: 1)
- p4-muse-3 (neck collar swatch):
  x: 245, y: 254, w: 44, h: 60 -> 73.6%, 60.6%, 13.2%, 14.3% (radius: 3px)
- p4-muse-4 (standing lavender girl back swatch):
  x: 245, y: 318, w: 28, h: 55 -> 73.6%, 75.9%, 8.4%, 13.1% (radius: 2px)
- p4-muse-5 (standing lavender girl full arch):
  x: 266, y: 286, w: 51, h: 112 -> 79.9%, 68.3%, 15.3%, 26.7% (radius: 999px 999px 0 0, z: 2)
*/

echo "Page 4 measured successfully.\n";

/*
PAGE 5:
Logo & Subtitle:
y: 18 to 48 -> 4.3%, h: 7.2%

Section 1: Silk Signature (top-left)
Heading: x: 22, y: 70, w: 107, h: 12 -> 6.5%, 16.7%, 31.7%, 2.9%
- p5-silk-1 (purple girl phone selfie arch):
  x: 19, y: 88, w: 53, h: 93 -> 5.6%, 21.0%, 15.7%, 22.2% (radius: 999px 999px 0 0)
- p5-silk-2 (purple neck border curved corner swatch):
  x: 18, y: 185, w: 53, h: 45 -> 5.3%, 44.2%, 15.7%, 10.7% (radius: 0 0 999px 999px)
- p5-silk-3 (purple back heart cutout):
  x: 75, y: 88, w: 45, h: 60 -> 22.3%, 21.0%, 13.4%, 14.3% (radius: 3px)
- p5-silk-4 (blue salwar standing girl):
  x: 74, y: 151, w: 45, h: 81 -> 22.0%, 36.0%, 13.4%, 19.3% (radius: 3px)
- p5-silk-5 (blue girl smiling face):
  x: 122, y: 88, w: 45, h: 60 -> 36.2%, 21.0%, 13.4%, 14.3% (radius: 3px)
- p5-silk-6 (blue neck scallop swatch):
  x: 122, y: 151, w: 45, h: 26 -> 36.2%, 36.0%, 13.4%, 6.2% (radius: 2px)
- p5-silk-7 (blue sleeve swatch):
  x: 122, y: 180, w: 45, h: 51 -> 36.2%, 43.0%, 13.4%, 12.2% (radius: 2px)

Section 2: Maternity Frock (top-right)
Heading: x: 167, y: 70, w: 150, h: 12 -> 49.6%, 16.7%, 44.5%, 2.9%
- p5-mat-1 (collar pin-tuck top swatch):
  x: 184, y: 88, w: 91, h: 39 -> 54.6%, 21.0%, 27.0%, 9.3% (radius: 2px)
- p5-mat-2 (sitting girl light blue frock):
  x: 184, y: 130, w: 45, h: 104 -> 54.6%, 31.0%, 13.4%, 24.8% (radius: 3px)
- p5-mat-3 (light blue frock back view):
  x: 231, y: 130, w: 44, h: 72 -> 68.5%, 31.0%, 13.1%, 17.2% (radius: 3px)
- p5-mat-4 (belly holding center inset swatch):
  x: 260, y: 172, w: 36, h: 33 -> 77.2%, 41.1%, 10.7%, 7.9% (radius: 2px, z: 2)
- p5-mat-5 (maternity floral dress standing model):
  x: 278, y: 88, w: 48, h: 114 -> 82.5%, 21.0%, 14.2%, 27.2% (radius: 4px)
- p5-mat-6 (floral neck swatch):
  x: 233, y: 206, w: 82, h: 28 -> 69.1%, 49.2%, 24.3%, 6.7% (radius: 2px, z: 1)

Section 3: Mandarin Camicia (bottom, full width)
Heading: x: 98, y: 247, w: 140, h: 12 -> 29.1%, 58.9%, 41.5%, 2.9%
- p5-cam-1 (teal shirt girl arch on left):
  x: 35, y: 268, w: 83, h: 116 -> 10.4%, 64.0%, 24.6%, 27.7% (radius: 999px 999px 0 0, z: 1)
- p5-cam-2 (teal shirt neck open swatch):
  x: 119, y: 268, w: 99, h: 54 -> 35.3%, 64.0%, 29.4%, 12.9% (radius: 3px)
- p5-cam-3 (teal button/collar closeup):
  x: 105, y: 308, w: 49, h: 48 -> 31.2%, 73.5%, 14.5%, 11.5% (radius: 3px, z: 2)
- p5-cam-4 (magenta open collar swatch):
  x: 119, y: 326, w: 98, h: 57 -> 35.3%, 77.8%, 29.1%, 13.6% (radius: 3px, z: 1)
- p5-cam-5 (magenta sleeve button swatch):
  x: 185, y: 362, w: 47, h: 45 -> 54.9%, 86.4%, 13.9%, 10.7% (radius: 2px, z: 3)
- p5-cam-6 (magenta shirt girl arch on right):
  x: 220, y: 268, w: 83, h: 116 -> 65.3%, 64.0%, 24.6%, 27.7% (radius: 999px 999px 0 0, z: 1)
*/

echo "Page 5 measured successfully.\n";
