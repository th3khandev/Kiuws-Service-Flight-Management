<?php

require_once __DIR__ . '/../constants/Kiws.php';
require_once __DIR__ . '/../utils/airlines.php';

class KiuwsService {
    private $baseUrl;
    private $xml;
    private $agentSine;
    private $termialID;
    private $user;
    private $password;
    private $mode;

    public function __construct($baseUrl, $agentSine, $termialID, $user, $password, $mode) {
        $this->baseUrl = $baseUrl;
        $this->agentSine = $agentSine;
        $this->termialID = $termialID;
        $this->user = $user;
        $this->password = $password;
        $this->mode = $mode;
    }

    private function createPOS_XmlObject() {
        // add POS xml object to xml
        $pos = $this->xml->addChild('POS');
        // add Source xml object to POS
        $pos->addChild('Source AgentSine="'. $this->agentSine .'" TerminalID="'. $this->termialID .'" ISOCountry="US" ISOCurrency="USD"');
    }

    private function createKIU_AirAvailRQXmlObject() {
        // Create the xml object
        $this->xml = new SimpleXMLElement('<KIU_AirAvailRQ EchoToken="1" TimeStamp="2013-01-24T19:20:43+00:00" Target="'. trim(ucfirst(strtolower($this->mode))) .'" Version="3.0"
        SequenceNmbr="1" PrimaryLangID="en-us" DirectFlightsOnly="false" MaxResponses="10"
        CombinedItineraries="false"></KIU_AirAvailRQ>');
        // Create the POS xml object
        $this->createPOS_XmlObject();
    }

    private function createOriginDestinationInformation ($depurateDate, $originLocation, $destinationLocation) {
        // add OriginDestinationInformation xml object to xml
        $originDestinationInformation = $this->xml->addChild('OriginDestinationInformation');
        // add DepartureDateTime xml object to OriginDestinationInformation
        $originDestinationInformation->addChild('DepartureDateTime', $depurateDate);
        // add OriginLocation xml object to OriginDestinationInformation
        $originDestinationInformation->addChild('OriginLocation LocationCode="'. $originLocation .'"');
        // add DestinationLocation xml object to OriginDestinationInformation
        $originDestinationInformation->addChild('DestinationLocation LocationCode="'. $destinationLocation .'"');
    }

    private function createTravelPreferences ($maxStopQuantity = 4) {
        // add TravelPreferences xml object to xml
        $travelPreferences = $this->xml->addChild('TravelPreferences');
        // Add MaxStopsQuantity Attribute to TravelPreferences
        $travelPreferences->addAttribute('MaxStopsQuantity', $maxStopQuantity);
        // add CabinPref xml object to TravelPreferences
        $cabinPref = $travelPreferences->addChild('CabinPref');
        // add Cabin Attribute to CabinPref
        $cabinPref->addAttribute('Cabin', 'Economy');
    }

    private function createTravelerInfoSummary ($adults = 1, $children = 1) {
        // add TravelerInfoSummary xml object to xml
        $travelerInfoSummary = $this->xml->addChild('TravelerInfoSummary');
        // add AirTravelerAvail xml object to TravelerInfoSummary
        $airTravelerAvail = $travelerInfoSummary->addChild('AirTravelerAvail');
        // add PassengerTypeQuantity xml object to AirTravelerAvail
        $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
        // add Code Attribute to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Code', 'ADT');
        // add Quantity Attribute to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Quantity', $adults);
        // add PassengerTypeQuantity xml object to AirTravelerAvail
        $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
        // add Code Attribute to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Code', 'CHD');
        // add Quantity Attribute to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Quantity', $children);
    }

    private function POST() {
        // Define the query string
        $request_args = array(
            'body' => [
                'request'   => $this->xml->asXML(),
                'user'      => $this->user,
                'password'  => $this->password
            ],
            'header' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'method' => 'POST'
        );

        // Define the arguments for the request
        $response = wp_remote_request($this->baseUrl, $request_args);

        // Validate the response
        if (is_wp_error($response)) {
            return [
                'status'    => 'error',
                'message'   => $response->get_error_message(),
                'flights'   => [],
            ];
        }

        // load xml response
        $response = simplexml_load_string($response['body']);

        // Convert to json
        $response = json_encode($response);

        // Convert to associative array
        $response = json_decode($response, true);

        // if error in flihts
        if (isset($response['Error'])) {
            return [
                'status'    => 'error',
                'message'   => getErrorMessage($response['Error']['ErrorCode']),
                'flights'   => [],
            ];
        }

        // Process the response
        return [
            'status'    => 'success',
            'message'   => '',
            'flights'   => $response['OriginDestinationInformation']
        ];
    }

    private function formatFlights ($apiResponse) {
        $flights = [];
        $depurateDate = $apiResponse['flights']['DepartureDateTime'];
        $originLocation = $apiResponse['flights']['OriginLocation'];
        $destinationLocation = $apiResponse['flights']['DestinationLocation'];

        $originDestinationOptions = $apiResponse['flights']['OriginDestinationOptions']['OriginDestinationOption'];
        if (isset($originDestinationOptions['FlightSegment'])) {
            $originDestinationOptions = [$originDestinationOptions];
        }

        // Forearch flights
        foreach ($originDestinationOptions as $originDestinationOption) {
            $flight = [
                'flightSegment' => [],
                'depurateDate'  => '',
                'arrivalDate'   => '',
                'duration'      => '',
                'stops'         => 0,
            ];

            $flightSegments = $originDestinationOption['FlightSegment'];
            if (isset($flightSegments['@attributes'])) {
                $flightSegments = [$flightSegments];
            }
            // Forearch flightSegment
            foreach ($flightSegments as $key => $flightSegment) {
                $airlineCode = $flightSegment['MarketingAirline']['@attributes']['CompanyShortName'];
                $_flightSegment = [
                    'departureDateTime'     => $flightSegment['@attributes']['DepartureDateTime'],
                    'arrivalDateTime'       => $flightSegment['@attributes']['ArrivalDateTime'],
                    'flightNumber'          => $flightSegment['@attributes']['FlightNumber'],
                    'stopQuantity'          => $flightSegment['@attributes']['StopQuantity'],
                    'journeyDuration'       => $flightSegment['@attributes']['JourneyDuration'],
                    'departureAirport'      => $flightSegment['DepartureAirport']['@attributes']['LocationCode'],
                    'arrivalAirport'        => $flightSegment['ArrivalAirport']['@attributes']['LocationCode'],
                    'marketingAirline'      => $airlineCode,
                    'marketingAirlineName'  => '',
                    'marketingAirlineLogo'  => '',
                    'equipment'             => $flightSegment['Equipment']['@attributes']['AirEquipType'],
                    'meal'                  => $flightSegment['Meal']['@attributes']['MealCode'],
                    'marketingCabin'        => [
                        'cabinType' => $flightSegment['MarketingCabin']['@attributes']['CabinType'],
                        'rph'       => $flightSegment['MarketingCabin']['@attributes']['RPH'],
                    ],
                    'bookingClassAvail' => [],
                ];

                if ($key > 0 && $key < count($flightSegments) - 1) {
                    $flight['stops']++;
                }

                // Get airline info
                $airline = getAirlineByCode($airlineCode);
                if (is_null($airline)) {
                    $airline = addAirlineToFile($airlineCode);
                }
                $_flightSegment['marketingAirlineName'] = $airline['name'];
                $_flightSegment['marketingAirlineLogo'] = $airline['logo'];

                // Forearch bookingClassAvail
                foreach ($flightSegment['BookingClassAvail'] as $bookingClassAvail) {
                    $_bookingClassAvail = [
                        'resBookDesigCode'      => $bookingClassAvail['@attributes']['ResBookDesigCode'],
                        'resBookDesigQuantity'  => $bookingClassAvail['@attributes']['ResBookDesigQuantity'],
                        'rph'                   => $bookingClassAvail['@attributes']['RPH'],
                    ];
                    array_push($_flightSegment['bookingClassAvail'], $_bookingClassAvail);
                }

                array_push($flight['flightSegment'], $_flightSegment);
            }
            // get depurate time flight
            $flight['depurateDate'] = $flight['flightSegment'][0]['departureDateTime'];
            // get arrival time flight
            $flight['arrivalDate'] = $flight['flightSegment'][count($flight['flightSegment']) - 1]['arrivalDateTime'];
            // get duration time flight (ArrivalDateTime - DepartureDateTime)
            $flight['duration'] = date('H:i', strtotime($flight['arrivalDate']) - strtotime($flight['depurateDate']));
            array_push($flights, $flight);
        }

        return [
            'depurateDate'          => $depurateDate,
            'originLocation'        => $originLocation,
            'destinationLocation'   => $destinationLocation,
            'flights'               => $flights
        ];
    }

    public function getAvailabilityFlights ($depurateDate, $originLocation, $destinationLocation, $adults = 1, $children = 1, $maxStopQuantity = 4) {
        // Create the xml object
        $this->createKIU_AirAvailRQXmlObject();
        // add OriginDestinationInformation xml object to xml
        $this->createOriginDestinationInformation($depurateDate, $originLocation, $destinationLocation);
        // add TravelPreferences xml object to xml
        $this->createTravelPreferences($maxStopQuantity);
        // add TravelerInfoSummary xml object to xml
        $this->createTravelerInfoSummary($adults, $children);
        // POST
        $response = $this->POST();
        if ($response['status'] === 'error') {
            return $response;
        }
        // Format response
        $result = $this->formatFlights($response);
        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'flights'               => $result['flights'],
            'depurateDate'          => $result['depurateDate'],
            'originLocation'        => $result['originLocation'],
            'destinationLocation'   => $result['destinationLocation'],
            'response'              => $response,
        ];
    }
}