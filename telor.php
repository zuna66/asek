<?php

// Fungsi untuk menolak akses
function denied() {
    header("HTTP/1.0 403 Forbidden");
    echo "<h1>Access Denied</h1>";
    exit();
}

// Mendapatkan URL lengkap untuk sitemap dan robots.txt
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$fullUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Memparsing URL lengkap
$parsedUrl = parse_url($fullUrl);
$scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

// Menghilangkan bagian file (seperti 'asek.php') jika ada dalam path
$basePath = dirname($path); // Mengambil direktori tanpa file PHP
$baseUrl = $scheme . "://" . $host . rtrim($basePath, '/');  // Pastikan tidak ada garis miring ganda

// Menyiapkan URL asli tanpa 'asek.php'
$urlAsli = $baseUrl . '/'; // Pastikan URL dasar adalah folder /video/

// Membuat robots.txt
$robotsTxt = "User-agent: *" . PHP_EOL;
$robotsTxt .= "Allow: /" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap-1.xml" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap-2.xml" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap-3.xml" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap-4.xml" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap-5.xml" . PHP_EOL;
file_put_contents('robots.txt', $robotsTxt);

// Array untuk menyimpan nama file yang akan diproses
$fileNames = array("note1.txt", "note2.txt", "note3.txt", "note4.txt", "note5.txt");

$currentSitemap = 1;
$sitemapFile = null;

// Proses setiap file dalam array
foreach ($fileNames as $filename) {
    if (file_exists($filename)) { // Pastikan file ada sebelum diproses
        $fileLines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (count($fileLines) > 0) { // Pastikan file tidak kosong
            // Membuka file sitemap untuk ditulis
            $sitemapFileName = "sitemap-$currentSitemap.xml";
            
            if ($currentSitemap == 1 || !file_exists($sitemapFileName)) {
                $sitemapFile = fopen($sitemapFileName, "w");

                // Menulis tag pembuka XML untuk sitemap pertama kali
                fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
                fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
            } else {
                $sitemapFile = fopen($sitemapFileName, "a");
            }

            // Menulis setiap URL dari file `note*.txt`
            foreach ($fileLines as $judul) {
                // Menghasilkan URL tanpa 'asek.php'
                $sitemapLink = $urlAsli . '?รังสี=' . urlencode($judul);
                fwrite($sitemapFile, '  <url>' . PHP_EOL);
                fwrite($sitemapFile, '    <loc>' . $sitemapLink . '</loc>' . PHP_EOL);
                date_default_timezone_set('Asia/Jakarta');
                $currentTime = date('Y-m-d\TH:i:sP');
                fwrite($sitemapFile, '    <lastmod>' . $currentTime . '</lastmod>' . PHP_EOL);
                fwrite($sitemapFile, '  </url>' . PHP_EOL);
            }

            // Menutup file setelah menulis URL
            fclose($sitemapFile);
            $currentSitemap++;
        }
    }
}

// Menyelesaikan semua file sitemap dengan menutup tag XML
for ($i = 1; $i <= $currentSitemap - 1; $i++) {
    $sitemapFile = fopen("sitemap-$i.xml", "a");
    fwrite($sitemapFile, '</urlset>' . PHP_EOL);  // Menutup tag </urlset>
    fclose($sitemapFile);
}

// Menampilkan pesan sukses
echo "<h1>SUDAH JADI ANJAY</h1>";
?>
