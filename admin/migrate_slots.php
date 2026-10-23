<?php
/**
 * Migration Script for Yarika Collections Slots & Sections
 * Run via CLI: php admin/migrate_slots.php
 *
 * Page 4 & Page 5 are TWO SEPARATE, INDEPENDENT PAGES (aspect-ratio: 4/5)
 * Each measured against its own page dimension.
 */
require_once __DIR__ . '/db.php';
$pdo = getDbConnection();
if (!$pdo) {
    die("Error: Could not connect to MySQL database.\n");
}

echo "Starting Migration for Yarika Collections (Independent Page 4 & Page 5)...\n";

// 1. Ensure catalog_pages has page4 and page5
$stmt = $pdo->prepare("
    INSERT INTO catalog_pages (id, title, subtitle)
    VALUES 
        ('page4', 'YARIKA™', 'COLLECTIONS'),
        ('page5', 'YARIKA™', 'COLLECTIONS')
    ON DUPLICATE KEY UPDATE 
        title = VALUES(title),
        subtitle = VALUES(subtitle)
");
$stmt->execute();
echo "-> catalog_pages verified.\n";

// 2. Ensure catalog_sections table
$pdo->exec("
CREATE TABLE IF NOT EXISTS catalog_sections (
  id VARCHAR(50) PRIMARY KEY,
  page_key VARCHAR(50) NOT NULL,
  label_ta VARCHAR(255),
  label_en VARCHAR(255),
  x DECIMAL(6,3),
  y DECIMAL(6,3),
  w DECIMAL(6,3),
  h DECIMAL(6,3),
  FOREIGN KEY (page_key) REFERENCES catalog_pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "-> Table catalog_sections verified.\n";

// 3. Ensure catalog_slots table
$pdo->exec("
CREATE TABLE IF NOT EXISTS catalog_slots (
  id VARCHAR(50) PRIMARY KEY,
  page_key VARCHAR(50) NOT NULL,
  section_id VARCHAR(50),
  x DECIMAL(6,3),
  y DECIMAL(6,3),
  w DECIMAL(6,3),
  h DECIMAL(6,3),
  radius VARCHAR(40) DEFAULT '0',
  z_index INT DEFAULT 1,
  image VARCHAR(500),
  focal_x DECIMAL(5,2) DEFAULT 50,
  focal_y DECIMAL(5,2) DEFAULT 50,
  zoom DECIMAL(4,2) DEFAULT 1.00,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (page_key) REFERENCES catalog_pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "-> Table catalog_slots verified.\n";

// 4. Seed Sections (Tamil — English)
// Page 4: Twirly (top-left), Edge Crop (top-right), Wave (bottom-left), Muse (bottom-right)
// Page 5: Silk Signature (top-left), Maternity Frock (top-right), Mandarin Camicia (bottom, full width)
$sections = [
    // Page 4 Sections
    [
        'id' => 'p4-twirly',
        'page_key' => 'page4',
        'label_ta' => 'சர்குலர் ஃப்ராக்',
        'label_en' => 'Twirly',
        'x' => 7.20, 'y' => 16.70, 'w' => 33.60, 'h' => 2.90
    ],
    [
        'id' => 'p4-edge-crop',
        'page_key' => 'page4',
        'label_ta' => 'வெஸ்டர்ன் டாப்',
        'label_en' => 'Edge Crop',
        'x' => 51.40, 'y' => 16.70, 'w' => 36.90, 'h' => 2.90
    ],
    [
        'id' => 'p4-wave',
        'page_key' => 'page4',
        'label_ta' => 'கேஷுவல் டாப்',
        'label_en' => 'Wave',
        'x' => 7.50, 'y' => 56.30, 'w' => 32.40, 'h' => 2.90
    ],
    [
        'id' => 'p4-muse',
        'page_key' => 'page4',
        'label_ta' => 'கேஷுவல் டாப்',
        'label_en' => 'Muse',
        'x' => 59.50, 'y' => 56.30, 'w' => 32.40, 'h' => 2.90
    ],

    // Page 5 Sections
    [
        'id' => 'p5-silk-signature',
        'page_key' => 'page5',
        'label_ta' => 'சுடிதார்',
        'label_en' => 'Silk Signature',
        'x' => 6.50, 'y' => 16.70, 'w' => 31.70, 'h' => 2.90
    ],
    [
        'id' => 'p5-maternity-frock',
        'page_key' => 'page5',
        'label_ta' => 'மகப்பேறு கால ஃப்ராக்',
        'label_en' => 'Maternity Frock',
        'x' => 49.60, 'y' => 16.70, 'w' => 44.50, 'h' => 2.90
    ],
    [
        'id' => 'p5-mandarin-camicia',
        'page_key' => 'page5',
        'label_ta' => 'லேடீஸ் ஷர்ட்',
        'label_en' => 'Mandarin Camicia',
        'x' => 29.10, 'y' => 58.90, 'w' => 41.50, 'h' => 2.90
    ]
];

$secStmt = $pdo->prepare("
    INSERT INTO catalog_sections (id, page_key, label_ta, label_en, x, y, w, h)
    VALUES (:id, :page_key, :label_ta, :label_en, :x, :y, :w, :h)
    ON DUPLICATE KEY UPDATE
        page_key = VALUES(page_key),
        label_ta = VALUES(label_ta),
        label_en = VALUES(label_en),
        x = VALUES(x),
        y = VALUES(y),
        w = VALUES(w),
        h = VALUES(h)
");

foreach ($sections as $sec) {
    $secStmt->execute($sec);
}
echo "-> 7 Sections inserted/updated.\n";

// 5. Seed Slots for Page 4 (Measured against Page 4 target 333x419)
$slots = [
    // === PAGE 4 SLOTS ===
    // Section: Twirly
    [
        'id' => 'p4-twirly-1', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 3.90, 'y' => 20.00, 'w' => 20.40, 'h' => 22.20,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-twirly-2', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 25.20, 'y' => 20.00, 'w' => 21.00, 'h' => 22.20,
        'radius' => '4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-twirly-3', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 3.30, 'y' => 43.00, 'w' => 11.40, 'h' => 9.80,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-twirly-4', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 15.30, 'y' => 43.00, 'w' => 9.60, 'h' => 9.80,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-twirly-5', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 25.50, 'y' => 43.00, 'w' => 9.90, 'h' => 9.80,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-twirly-6', 'page_key' => 'page4', 'section_id' => 'p4-twirly',
        'x' => 36.00, 'y' => 43.00, 'w' => 10.20, 'h' => 9.80,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-twirly-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // Section: Edge Crop
    [
        'id' => 'p4-edge-1', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 50.20, 'y' => 21.00, 'w' => 13.80, 'h' => 31.50,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-edge-2', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 65.20, 'y' => 21.00, 'w' => 12.60, 'h' => 9.50,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-edge-3', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 65.20, 'y' => 31.30, 'w' => 12.60, 'h' => 10.50,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-edge-4', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 65.20, 'y' => 42.50, 'w' => 12.60, 'h' => 9.50,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-edge-5', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 79.00, 'y' => 21.00, 'w' => 14.10, 'h' => 8.60,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-edge-6', 'page_key' => 'page4', 'section_id' => 'p4-edge-crop',
        'x' => 79.00, 'y' => 30.30, 'w' => 14.10, 'h' => 22.20,
        'radius' => '4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-edge-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // Section: Wave
    [
        'id' => 'p4-wave-1', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 3.60, 'y' => 60.10, 'w' => 27.00, 'h' => 10.50,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-wave-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-wave-2', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 3.60, 'y' => 71.60, 'w' => 13.50, 'h' => 10.50,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-wave-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-wave-3', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 6.60, 'y' => 78.80, 'w' => 14.10, 'h' => 16.20,
        'radius' => '4px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-4/p4-wave-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-wave-4', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 18.00, 'y' => 71.60, 'w' => 12.60, 'h' => 15.00,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-wave-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-wave-5', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 21.30, 'y' => 87.40, 'w' => 19.20, 'h' => 7.60,
        'radius' => '3px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-4/p4-wave-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-wave-6', 'page_key' => 'page4', 'section_id' => 'p4-wave',
        'x' => 31.50, 'y' => 60.10, 'w' => 13.50, 'h' => 26.30,
        'radius' => '4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-wave-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // Section: Muse
    [
        'id' => 'p4-muse-1', 'page_key' => 'page4', 'section_id' => 'p4-muse',
        'x' => 48.40, 'y' => 75.20, 'w' => 11.40, 'h' => 13.40,
        'radius' => '3px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-4/p4-muse-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-muse-2', 'page_key' => 'page4', 'section_id' => 'p4-muse',
        'x' => 52.60, 'y' => 60.60, 'w' => 19.80, 'h' => 34.40,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-muse-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-muse-3', 'page_key' => 'page4', 'section_id' => 'p4-muse',
        'x' => 73.60, 'y' => 60.60, 'w' => 13.20, 'h' => 14.30,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-muse-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-muse-4', 'page_key' => 'page4', 'section_id' => 'p4-muse',
        'x' => 73.60, 'y' => 75.90, 'w' => 8.40, 'h' => 13.10,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-4/p4-muse-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p4-muse-5', 'page_key' => 'page4', 'section_id' => 'p4-muse',
        'x' => 79.90, 'y' => 68.30, 'w' => 15.30, 'h' => 26.70,
        'radius' => '999px 999px 4px 4px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-4/p4-muse-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // === PAGE 5 SLOTS (Measured against Page 5 target 337x419) ===
    // Section: Silk Signature
    [
        'id' => 'p5-silk-1', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 5.60, 'y' => 21.00, 'w' => 15.70, 'h' => 22.20,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-2', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 5.30, 'y' => 44.20, 'w' => 15.70, 'h' => 10.70,
        'radius' => '4px 4px 999px 999px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-3', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 22.30, 'y' => 21.00, 'w' => 13.40, 'h' => 14.30,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-4', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 22.00, 'y' => 36.00, 'w' => 13.40, 'h' => 19.30,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-5', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 36.20, 'y' => 21.00, 'w' => 13.40, 'h' => 14.30,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-6', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 36.20, 'y' => 36.00, 'w' => 13.40, 'h' => 6.20,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-silk-7', 'page_key' => 'page5', 'section_id' => 'p5-silk-signature',
        'x' => 36.20, 'y' => 43.00, 'w' => 13.40, 'h' => 12.20,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-silk-7.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // Section: Maternity Frock
    [
        'id' => 'p5-mat-1', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 54.60, 'y' => 21.00, 'w' => 27.00, 'h' => 9.30,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-mat-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-mat-2', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 54.60, 'y' => 31.00, 'w' => 13.40, 'h' => 24.80,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-mat-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-mat-3', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 68.50, 'y' => 31.00, 'w' => 13.10, 'h' => 17.20,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-mat-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-mat-4', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 77.20, 'y' => 41.10, 'w' => 10.70, 'h' => 7.90,
        'radius' => '2px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-5/p5-mat-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-mat-5', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 82.50, 'y' => 21.00, 'w' => 14.20, 'h' => 27.20,
        'radius' => '4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-mat-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-mat-6', 'page_key' => 'page5', 'section_id' => 'p5-maternity-frock',
        'x' => 69.10, 'y' => 49.20, 'w' => 24.30, 'h' => 6.70,
        'radius' => '2px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-mat-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],

    // Section: Mandarin Camicia
    [
        'id' => 'p5-cam-1', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 10.40, 'y' => 64.00, 'w' => 24.60, 'h' => 27.70,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-cam-1.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-cam-2', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 35.30, 'y' => 64.00, 'w' => 29.40, 'h' => 12.90,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-cam-2.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-cam-3', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 31.20, 'y' => 73.50, 'w' => 14.50, 'h' => 11.50,
        'radius' => '3px', 'z_index' => 2,
        'image' => '../global assets/Images/products/page-5/p5-cam-3.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-cam-4', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 35.30, 'y' => 77.80, 'w' => 29.10, 'h' => 13.60,
        'radius' => '3px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-cam-4.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-cam-5', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 54.90, 'y' => 86.40, 'w' => 13.90, 'h' => 10.70,
        'radius' => '2px', 'z_index' => 3,
        'image' => '../global assets/Images/products/page-5/p5-cam-5.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ],
    [
        'id' => 'p5-cam-6', 'page_key' => 'page5', 'section_id' => 'p5-mandarin-camicia',
        'x' => 65.30, 'y' => 64.00, 'w' => 24.60, 'h' => 27.70,
        'radius' => '999px 999px 4px 4px', 'z_index' => 1,
        'image' => '../global assets/Images/products/page-5/p5-cam-6.webp',
        'focal_x' => 50, 'focal_y' => 50, 'zoom' => 1.00
    ]
];

$slotStmt = $pdo->prepare("
    INSERT INTO catalog_slots (id, page_key, section_id, x, y, w, h, radius, z_index, image, focal_x, focal_y, zoom)
    VALUES (:id, :page_key, :section_id, :x, :y, :w, :h, :radius, :z_index, :image, :focal_x, :focal_y, :zoom)
    ON DUPLICATE KEY UPDATE
        page_key = VALUES(page_key),
        section_id = VALUES(section_id),
        x = VALUES(x),
        y = VALUES(y),
        w = VALUES(w),
        h = VALUES(h),
        radius = VALUES(radius),
        z_index = VALUES(z_index),
        image = VALUES(image)
");

foreach ($slots as $slot) {
    $slotStmt->execute($slot);
}
echo "-> " . count($slots) . " Slots inserted/updated.\n";

echo "Migration finished successfully!\n";
