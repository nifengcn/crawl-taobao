<?php
include_once("../lib/simple_html_dom/simple_html_dom.php");

$dom = file_get_html("https://www.taobao.com");

$ul = $dom->find("ul.service-bd", 0);
$idx = 0;
$filename = "../data/category.dat";
file_put_contents($filename, "idx\tcategory" . PHP_EOL);
foreach ($ul->find("a") as $href) {
    file_put_contents($filename, $idx . "\t" . $href->innertext . PHP_EOL, FILE_APPEND);
    $idx++;
}
