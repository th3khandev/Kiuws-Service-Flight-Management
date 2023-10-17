<?php

$dirAirlineLogos = plugin_dir_url(__FILE__) . '/../assets/images/airlines/';
$logoDefault = $dirAirlineLogos . 'default.png';

$airlinesLogoDefault = [
    '9R' => $dirAirlineLogos . '9R.png',
    'AA' => $dirAirlineLogos . 'AA.png',
    '9V' => $dirAirlineLogos . '9V.webp',
    'KN' => $dirAirlineLogos . 'KN.webp',
    'L5' => $dirAirlineLogos . 'L5.png',
];

function getAirlineByCode ($code) {
    // open file
    $fileAirline = file_get_contents(__DIR__ . '/airlines.json');
    // decode json to array
    $airlines = json_decode($fileAirline, true);
    // search airline by code
    foreach ($airlines as $airline) {
        if ($airline['code'] == $code) {
            return $airline;
        }
    }    
    return null;
}

function addAirlineToFile ($code) {
    // make GET to https://www.iata.org/PublicationDetails/Search/?currentBlock=314383
    $response = wp_remote_get('https://www.iata.org/PublicationDetails/Search/?currentBlock=314383&airline.search=' . $code);
    if (is_wp_error($response)) {
        return null;
    }
    // get html
    $html = wp_remote_retrieve_body($response);
    // parse html
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    // get table with class datatable
    $table = $xpath->query('//table[@class="datatable"]')->item(0); // <table class="datatable"><thead> <tr><td>Company name</td><td>Country / Territory</td><td>2-letter code</td></tr></thead><tbody><tr><td>Air China Cargo Co., Ltd</td><td>People&#x27;s Republic of China</td><td>CA*</td></tr> <tr> <td>Air China LTD</td><td>People&#x27;s Republic of China</td><td>CA</td></tr></tbody></table>
    // get rows into tbody
    $rows = $table->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
    // get airline
    $airline = [];
    foreach ($rows as $row) {
        $tds = $row->getElementsByTagName('td');
        if ($tds->length == 3) {
            $airline['name'] = $tds->item(0)->nodeValue;
            $airline['country'] = $tds->item(1)->nodeValue;
            $airline['code'] = $code;
            $airline['logo'] = '';
            if (isset($GLOBALS['airlinesLogoDefault'][$code])) {
                $airline['logo'] = $GLOBALS['airlinesLogoDefault'][$code];
            } else {
                $airline['logo'] = $GLOBALS['logoDefault'];
            }
            break;
        }
    }
    // add airline to file
    $fileAirline = file_get_contents(__DIR__ . '/airlines.json');
    // decode json to array
    $airlines = json_decode($fileAirline, true);
    // add airline
    $airlines[] = $airline;
    // encode array to json
    $fileAirline = json_encode($airlines);
    // save file
    file_put_contents(__DIR__ . '/airlines.json', $fileAirline);
    return $airline;
}