<?php
$file = 'vendor/composer/autoload_psr4.php';
if (!file_exists($file)) {
    die("Autoloader file not found!");
}

$content = file_get_contents($file);

$newEntries = "    'PragmaRX\\\\Google2FA\\\\' => array(\$vendorDir . '/pragmarx/google2fa/src'),\n" .
              "    'PragmaRX\\\\Google2FAQRCode\\\\' => array(\$vendorDir . '/pragmarx/google2fa-qrcode/src'),\n" .
              "    'BaconQrCode\\\\' => array(\$vendorDir . '/bacon/bacon-qr-code/src'),\n";

if (strpos($content, 'PragmaRX\\\\Google2FA') === false) {
    // Insert after 'return array('
    $content = str_replace("return array(", "return array(\n" . $newEntries, $content);
    file_put_contents($file, $content);
    echo "Autoloader fixed successfully!\n";
} else {
    echo "Autoloader already has PragmaRX entries.\n";
}
