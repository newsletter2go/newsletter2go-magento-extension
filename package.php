<?php

$date = date('Y-m-d');
$time = date('H:i:s');
$template = <<<XML
<?xml version="1.0"?>
<package>
    <name>NL2go_Sync</name>
    <version>3.2.05</version>
    <stability>stable</stability>
    <license>GPL</license>
    <channel>community</channel>
    <extends/>
    <summary>Synchronizes your Magento newsletter subscribers, sales figures and shop items with Newsletter2Go</summary>
    <description>The Newsletter2Go email marketing software extension for Magento automatically synchronizes all
newsletter subscribers, including basic data like name and gender as well as the total amount of sales and
average sale.&#xD;
Additionally, you can easily retrieve all item data including description, price info, product images and
product link just by inputting the item id in the Newsletter2Go software.</description>
    <notes>- New features for magento-integration in Newsletter2Go&#xD;
- Added config page and connect button, implemented tracking option&#xD;
- Compatibility with enterprise version&#xD;
- Bug fixes</notes>
    <authors>
        <author>
            <name>Newsletter2Go GmbH</name>
            <user>Newsletter2Go</user>
            <email>integrations@newsletter2go.de</email>
        </author>
    </authors>
    <date>$date</date>
    <time>$time</time>
    <contents/>
    <compatible/>
    <dependencies>
        <required>
            <php>
                <min>5.4.0</min>
                <max>7.1.0</max>
            </php>
        </required>
    </dependencies>
</package>
XML;

$domDoc = new \DOMDocument('1.0', 'UTF-8');
$domDoc->preserveWhiteSpace = false;
$domDoc->formatOutput = true;
$domDoc->loadXML($template);
$contents = $domDoc->getElementsByTagName('contents')->item(0);

$targets = [
    'etc' => ['name' => 'mageetc', 'skip' => 1],
    'code' => ['name' => 'magecommunity', 'skip' => 2],
    'design' => ['name' => 'magedesign', 'skip' => 1],
];

function addContent(\DOMDocument $domDoc, \DOMElement $parentNode, $path, $skip)
{
    if (is_dir($path)) {
        if ($skip > 0) {
            $skip --;
        } else {
            $dirNode = $domDoc->createElement('dir');
            $dirNode->setAttribute('name', basename($path));
            $parentNode->appendChild($dirNode);
            $parentNode = $dirNode;
        }
        foreach (glob($path . '/*') as $child) {
            addContent($domDoc, $parentNode, $child, $skip);
        }
    } elseif (is_file($path)) {
        $fileNode = $domDoc->createElement('file');
        $fileNode->setAttribute('name', basename($path));
        $fileNode->setAttribute('hash', md5_file($path));
        $parentNode->appendChild($fileNode);
    } else {
        throw new \RuntimeException("Unsupported filesystem object: $path");
    }
}
foreach(glob(__DIR__ . '/app/*') as $dir) {
    $name = basename($dir);

    if (!isset($targets[$name])) {
        throw new \RuntimeException("Unsupported target type: $name");
    }

    $targetNode = $domDoc->createElement('target');
    $targetNode->setAttribute('name', $targets[$name]['name']);
    $contents->appendChild($targetNode);

    addContent($domDoc, $targetNode, $dir, $targets[$name]['skip']);
}

$domDoc->save(__DIR__ . '/package.xml');
