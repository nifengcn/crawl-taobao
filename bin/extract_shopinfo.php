<?php
include_once("../lib/simple_html_dom/simple_html_dom.php");

function get_html_retry($url) {
    $cnt = 0;
    while (true) {
        $cnt++;
        var_dump(['desc' => 'crawl dom', 'url' => $url, 'cnt' => $cnt]);
        $dom = file_get_html($url);
        if ($dom) {
            return $dom;
        }
    }
}

function get_json($url) {
    $dom = get_html_retry($url);
    $script_dom = $dom->find("script", 5);
    $config = trim(explode("g_srp_loadCss", $script_dom->innertext)[0]);
    $config = substr($config, 0, strlen($config) - 1);
    $shops = json_decode(explode('=', $config)[1], true);
    if (is_array($shops)) {
        return $shops;
    } else {
        var_dump($script_dom->innertext);
        die;
    }
}

function get_shops_retry($url) {
    $idx = 0;
    while (true) {
        $idx++;
        $shops_select_page = get_json($url);
        var_dump(['desc' => 'shopsinfo', 'url' => $url, 'idx' => $idx]);
        if (is_array($shops_select_page['mods']['shoplist']['data']['shopItems'])) {
            return $shops_select_page['mods']['shoplist']['data']['shopItems'];
        }
        if (is_array($shops_select_page['mods']['shopList']['data']['shopItems'])) {
            return $shops_select_page['mods']['shopList']['data']['shopItems'];
        }
        var_dump($shops_select_page);
    } 
}

function extract_data($category) {
    $filename = "../data/{$category}.dat";
    $url = 'https://shopsearch.taobao.com/search?q=' . urlencode($category) . '&app=shopsearch&spm=a217f.8051907.1000187.3';
    $shops = get_json($url);
    $pageSize = $shops['mods']['pager']['data']['pageSize'];
    $totalPage = $shops['mods']['pager']['data']['totalPage'];
    $totalCount = $shops['mods']['pager']['data']['totalCount'];
    $twoThousand = [];
    $twoThousand_totalsold = [];
    file_put_contents($filename, "品类\t店铺总数" . PHP_EOL);
    file_put_contents($filename, $category . "\t" . $totalCount . PHP_EOL, FILE_APPEND);
    file_put_contents($filename, PHP_EOL, FILE_APPEND);

    for ($idx = 0; $idx < $totalPage; $idx++) {
        $url_select_page = $url . '&s=' . $idx * $pageSize;
        $shopsItems = get_shops_retry($url_select_page);
        foreach ($shopsItems as $key => $value) {
            $twoThousand_totalsold[] = $value['totalsold'];
            $twoThousand[] = [$value['title'], $value['totalsold'], $value['goodratePercent']];
        }
    }
    array_multisort($twoThousand_totalsold, SORT_DESC, SORT_NUMERIC, $twoThousand);
    file_put_contents($filename, "店铺名称\t销量\t好评率" . PHP_EOL, FILE_APPEND);
    foreach ($twoThousand as $item) {
        file_put_contents($filename, $item[0] . "\t" . $item[1] . "\t" . $item[2] . PHP_EOL, FILE_APPEND);
    }
    file_put_contents($filename, PHP_EOL, FILE_APPEND);

    $regions = [
        '北京',
        '上海',
        '广州',
        '深圳',
        '杭州',
        '海外',
        '江浙沪',
        '珠三角',
        '京津冀',
        '东三省',
        '港澳台',
        '江浙沪皖',
        '长沙',
        '长春',
        '成都',
        '重庆',
        '大连',
        '东莞',
        '佛山',
        '福州',
        '贵阳',
        '合肥',
        '金华',
        '济南',
        '嘉兴',
        '昆明',
        '宁波',
        '南昌',
        '南京',
        '青岛',
        '泉州',
        '沈阳',
        '苏州',
        '天津',
        '温州',
        '无锡',
        '武汉',
        '西安',
        '厦门',
        '郑州',
        '中山',
        '石家庄',
        '哈尔滨',
        '安徽',
        '福建',
        '甘肃',
        '广东',
        '广西',
        '贵州',
        '海南',
        '河北',
        '河南',
        '湖北',
        '湖南',
        '江苏',
        '江西',
        '吉林',
        '辽宁',
        '宁夏',
        '青海',
        '山东',
        '山西',
        '陕西',
        '云南',
        '四川',
        '西藏',
        '新疆',
        '浙江',
        '澳门',
        '香港',
        '台湾',
        '内蒙古',
        '黑龙江',
        ];
    file_put_contents($filename, "地区\t店铺数" . PHP_EOL, FILE_APPEND);
    foreach ($regions as $item) {
        $url_select_region =  $url . '&loc=' . urlencode($item);
        $shops = get_json($url_select_region);
        $totalCount = $shops['mods']['pager']['data']['totalCount'];
        file_put_contents($filename, $item . "\t" . $totalCount . PHP_EOL, FILE_APPEND);
    }
}

function main() {
    $dom = get_html_retry("https://www.taobao.com");
    $ul = $dom->find("ul.service-bd", 0);
    foreach ($ul->find("a") as $href) {
        var_dump($href->innertext);
        extract_data($href->innertext);
    }
}

main();
//get_json("https://shopsearch.taobao.com/search?q=%E5%A5%B3%E8%A3%85&app=shopsearch&spm=a217f.8051907.1000187.3&s=460");
//https://shopsearch.taobao.com/search?q=%E5%A5%B3%E8%A3%85&app=shopsearch&spm=a217f.8051907.1000187.3&loc=%E5%8C%97%E4%BA%AC
