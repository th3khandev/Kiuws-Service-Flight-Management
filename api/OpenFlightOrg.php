<?php

class OpenFlightsOrg {
    // url api 
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = 'https://openflights.org/php/apsearch.php';
    }

    private function formatResponse($responseApi) {
        $response = [
            'status'    => $responseApi['status'],
            'offset'    => $responseApi['offset'],
            'total'     => $responseApi['max'],
            'airports'  => []
        ];
        foreach ($responseApi['airports'] as $airport) {
            if ($airport['type'] == 'airport') {
                $response['airports'][] = [
                    'name'          => str_replace('Airport', '', $airport['name']),
                    'city'          => $airport['city'],
                    'country'       => $airport['country'],
                    'country_code'  => $airport['country_code'],
                    'source'        => $airport['source'],
                    'type'          => $airport['type'],
                    'code'          => $airport['iata']
                ];
            }
        }
        return $response;
    }

    public function getAirports($search) {
        // define parameters
        $params = [
            'action'    => 'SEARCH',
            'city'      => $search,
            // 'name'      => $search,
            'country'   => 'ALL'
        ];

        // Define the query string
        $request_args = array(
            'body' => $params,
            'header' => array(
                'Content-Type' => 'multipart/form-data'
            ),
            'method' => 'POST'
        );

        // Define the arguments for the request
        $response = wp_remote_request($this->baseUrl, $request_args);

        // Validate the response
        if (is_wp_error($response)) {
            return array(); // TODO: Handle error
        }

        // Process the response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // $data now contains the airport codes data
        return $this->formatResponse($data);
    }
}