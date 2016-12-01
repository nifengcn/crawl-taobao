<?php
include_once("../lib/simple_html_dom/simple_html_dom.php");

$dom = file_get_html("https://shopsearch.taobao.com/search?q=%E5%A5%B3%E8%A3%85&app=shopsearch&spm=a217f.8051907.1000187.3");
$script_dom = $dom->find("script", 5);
$config = explode(";  ", $script_dom->innertext)[0];
$shops = json_decode(explode('=', $config)[1], true);
$pageSize = $shops['mods']['pager']['data']['pageSize'];
var_dump($pageSize);
$totalPage = $shops['mods']['pager']['data']['totalPage'];
var_dump($totalPage);
foreach($shops['mods']['shoplist']['data']['shopItems'] as $key => $value) {
    var_dump($value['uid']);
    var_dump($value['title']);
    var_dump($value['nick']);
}
