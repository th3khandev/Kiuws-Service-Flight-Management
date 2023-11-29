<?php

namespace  Kiuws_Service_Flight_Management\Services;

use DOMDocument;
use DOMXPath;

class IataOrg {
    private $dirAirlineLogos = 'assets/images/airlines/';
    private $logoDefault = '';
    private $airlinesLogoDefault = [];
    private $airlinesDbFile = FLIGHT_MANAGEMENT_DIR . 'db/json/airlines.json';
    private $airlines_extras = [
        [
            'code' => 'G0',
            'name' => 'Albatros Airlines',
            'country' => 'Venezuela'
        ]
    ];

    public function __construct()
    {
        $this->logoDefault = $this->dirAirlineLogos . 'default.png';
        $this->airlinesLogoDefault = [
            '9R'    => $this->dirAirlineLogos . '9R.png',
            'AA'    => $this->dirAirlineLogos . 'AA.png',
            '9V'    => $this->dirAirlineLogos . '9V.webp',
            'KN'    => $this->dirAirlineLogos . 'KN.webp',
            'L5'    => $this->dirAirlineLogos . 'L5.png',
            'AV'    => $this->dirAirlineLogos . 'AV.png',
            'LA'    => $this->dirAirlineLogos . 'LA.png',
            'UA'    => $this->dirAirlineLogos . 'UA.png',
            '5R'    => $this->dirAirlineLogos . '5R.png',
            'QL'    => $this->dirAirlineLogos . 'QL.png',
        ];
    }

    public function getAirlineByCode ($code) {
        // open file
        $fileAirline = file_get_contents($this->airlinesDbFile);
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

    private function getAirlineExtraByCode ($code) {
        foreach ($this->airlines_extras as $airline) {
            if ($airline['code'] == $code) {
                return $airline;
            }
        }
        return [
            'code' => $code,
            'name' => 'Airline not found',
            'country' => ''
        ];
    }

    public function addAirlineToFile ($code) {
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
        $table = $xpath->query('//table[@class="datatable"]')->item(0);

        $airline = [];

        if (is_null($table)) {
            $airline = $this->getAirlineExtraByCode($code);
            $airline['logo'] = $this->logoDefault;
            if (isset($this->airlinesLogoDefault[$code])) {
                $airline['logo'] = $this->airlinesLogoDefault[$code];
            }
        } else {
            // get rows into tbody
            $rows = $table->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
            // get airline
            foreach ($rows as $row) {
                $tds = $row->getElementsByTagName('td');
                if ($tds->length == 3) {
                    $airline['name'] = $tds->item(0)->nodeValue;
                    $airline['country'] = $tds->item(1)->nodeValue;
                    $airline['code'] = $code;
                    $airline['logo'] = '';
                    if (isset($this->airlinesLogoDefault[$code])) {
                        $airline['logo'] = $this->airlinesLogoDefault[$code];
                    } else {
                        $airline['logo'] = $this->logoDefault;
                    }
                    break;
                }
            }

        }
        // add airline to file
        $fileAirline = file_get_contents($this->airlinesDbFile);
        // decode json to array
        $airlines = json_decode($fileAirline, true);
        // add airline
        $airlines[] = $airline;
        // encode array to json
        $fileAirline = json_encode($airlines);
        // save file
        file_put_contents($this->airlinesDbFile, $fileAirline);
        return $airline;
    }
}