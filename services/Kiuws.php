<?php

namespace Kiuws_Service_Flight_Management\Services;

use SimpleXMLElement;

class Kiuws {
    private $baseUrl;
    private $xml;
    private $agentSine;
    private $termialID;
    private $user;
    private $password;
    private $mode;
    private $errors = [
        '10035'     => 'La fecha de salida no debe ser pasada ni superior a 330 días.',
        '10026'     => 'The TerminalID is not an authorized device',
        '11005'     => 'Error at parameter DepartureDateTime.'
    ];
    private $iataServices;

    public function __construct($baseUrl, $agentSine, $termialID, $user, $password, $mode) {
        $this->baseUrl = $baseUrl;
        $this->agentSine = $agentSine;
        $this->termialID = $termialID;
        $this->user = $user;
        $this->password = $password;
        $this->mode = $mode;
        $this->iataServices = new IataOrg();
    }

    private function getErrorMessage($code) {
        if (isset($this->errors[$code])) {
            return $this->errors[$code];
        }
        return 'Error desconocido';
    }

    private function createPOS_XmlObject($requestorID = null, $bookingChannel = null) {
        // add POS xml object to xml
        $pos = $this->xml->addChild('POS');
        // add Source xml object to POS
        $pos->addChild('Source AgentSine="'. $this->agentSine .'" TerminalID="'. $this->termialID .'" ISOCountry="US" ISOCurrency="USD"');
        // add RequestorID xml object to POS
        if (!is_null($requestorID)) {
            $pos->addChild('RequestorID Type="'. $requestorID .'"');
        }
        // add BookingChannel xml object to POS
        if (!is_null($bookingChannel)) {
            $pos->addChild('BookingChannel Type="'. $bookingChannel .'"');
        }
    }

    private function createKIU_AirAvailRQXmlObject() {
        // Create the xml object
        $this->xml = new SimpleXMLElement('<KIU_AirAvailRQ EchoToken="1" TimeStamp="2013-01-24T19:20:43+00:00" Target="'. trim(ucfirst(strtolower($this->mode))) .'" Version="3.0"
        SequenceNmbr="1" PrimaryLangID="en-us" DirectFlightsOnly="false" MaxResponses="10"
        CombinedItineraries="false"></KIU_AirAvailRQ>');
        // Create the POS xml object
        $this->createPOS_XmlObject();
    }

    private function createKIU_AirPriceRQObject() {
        // Create the xml object
        $this->xml = new SimpleXMLElement('<KIU_AirPriceRQ EchoToken="1" TimeStamp="2013-01-24T19:20:43+00:00" Target="'. trim(ucfirst(strtolower($this->mode))) .'" Version="3.0"
        SequenceNmbr="1" PrimaryLangID="en-us"></KIU_AirPriceRQ>');
        // Create the POS xml object
        $this->createPOS_XmlObject(5, 1);
    }

    private function createAirItineraryXmlObject ($depurateDateTime, $arrivalDateTime, $flightNumber, $resBookDesigCode, $originLocation, $destinationLocation, $airlineCode) {
        // add AirItinerary xml object to xml
        $airItinerary = $this->xml->addChild('AirItinerary');
        // add OriginDestinationOptions xml object to AirItinerary
        $originDestinationOptions = $airItinerary->addChild('OriginDestinationOptions');
        // add OriginDestinationOption xml object to OriginDestinationOptions
        $originDestinationOption = $originDestinationOptions->addChild('OriginDestinationOption');
        // add FlightSegment xml object to OriginDestinationOption
        $flightSegment = $originDestinationOption->addChild('FlightSegment');
        // add DepurateDateTime, ArrivalDateTime, FlightNumber, ResBookDesigCode attributtes to FlightSegment
        $flightSegment->addAttribute('DepartureDateTime', $depurateDateTime);
        $flightSegment->addAttribute('ArrivalDateTime', $arrivalDateTime);
        $flightSegment->addAttribute('FlightNumber', $flightNumber);
        $flightSegment->addAttribute('ResBookDesigCode', $resBookDesigCode);
        // add DepartureAirport xml object to FlightSegment
        $flightSegment->addChild('DepartureAirport LocationCode="'. $originLocation .'"');
        // add ArrivalAirport xml object to FlightSegment
        $flightSegment->addChild('ArrivalAirport LocationCode="'. $destinationLocation .'"');
        // add MarketingAirline xml object to FlightSegment
        $flightSegment->addChild('MarketingAirline Code="'. $airlineCode .'"');
    }

    private function createTravelerInfoSummaryXmlObject($adults = 1, $children = 0) {
        // add TravelerInfoSummary to xml
        $travelerInfoSummary = $this->xml->addChild('TravelerInfoSummary');
        // add AirTravelerAvail to TravelerInfoSummary
        $airTravelerAvail = $travelerInfoSummary->addChild('AirTravelerAvail');
        // add PassengerTypeQuantity to AirTravelerAvail
        $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
        // add Code, Quantity attributtes to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Code', 'ADT');
        $passengerTypeQuantity->addAttribute('Quantity', $adults);
        // add PassengerTypeQuantity to AirTravelerAvail
        $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
        // add Code, Quantity attributtes to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Code', 'CNN');
        $passengerTypeQuantity->addAttribute('Quantity', $children);
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
                'message'   => $this->getErrorMessage($response['Error']['ErrorCode']),
                'response'  => [],
            ];
        }

        // Process the response
        return [
            'status'    => 'success',
            'message'   => '',
            'response'   => $response
        ];
    }

    private function formatFlights ($apiResponse) {
        $flights = [];
        $depurateDate = $apiResponse['DepartureDateTime'];
        $originLocation = $apiResponse['OriginLocation'];
        $destinationLocation = $apiResponse['DestinationLocation'];

        $originDestinationOptions = $apiResponse['OriginDestinationOptions']['OriginDestinationOption'];
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
                $airline = $this->iataServices->getAirlineByCode($airlineCode);
                if (is_null($airline)) {
                    $airline = $this->iataServices->addAirlineToFile($airlineCode);
                }
                $_flightSegment['marketingAirlineName'] = $airline['name'];
                $_flightSegment['marketingAirlineLogo'] = FLIGHT_MANAGEMENT_URL . $airline['logo'];

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

    private function formatFlightPrice ($apiResponse) {
        /* format $apiResponse = <?xml version="1.0" encoding="UTF-8"?><KIU_AirPriceRS EchoToken="1" TimeStamp="2023-10-24T00:32:59+00:00" Target="Testing" Version="3.0" SequenceNmbr="1"><Success/><PricedItineraries><PricedItinerary SequenceNumber="1"><AirItinerary><OriginDestinationOptions><OriginDestinationOption><FlightSegment DepartureDateTime="2023-10-28 09:30:00" ArrivalDateTime="2023-10-28 12:00:00" FlightNumber="301" ResBookDesigCode="Y"><DepartureAirport LocationCode="MIA"/><ArrivalAirport LocationCode="PUJ"/><MarketingAirline Code="KN"/></FlightSegment></OriginDestinationOption></OriginDestinationOptions></AirItinerary><AirItineraryPricingInfo><ItinTotalFare><BaseFare Amount="87.73" CurrencyCode="USD"/><EquivFare Amount="87.73" CurrencyCode="USD"/><Taxes><Tax TaxCode="AY" Amount="5.6" CurrencyCode="USD"/><Tax TaxCode="US" Amount="21.1" CurrencyCode="USD"/><Tax TaxCode="XF" Amount="4.5" CurrencyCode="USD"/><Tax TaxCode="YQ" Amount="80" CurrencyCode="USD"/></Taxes><TPA_Extension><Surcharges><Surcharge FareChargeCode="" FareChargeAmount="0.00" /></Surcharges></TPA_Extension><TotalFare Amount="198.93" CurrencyCode="USD"/></ItinTotalFare><PTC_FareBreakdowns><PTC_FareBreakdown><PassengerTypeQuantity Quantity="1" Code="ADT" /><PassengerFare><BaseFare Amount="87.73" CurrencyCode="USD"/><EquivFare Amount="87.73" CurrencyCode="USD"/><Taxes><Tax TaxCode="AY" Amount="5.6" CurrencyCode="USD" /><Tax TaxCode="US" Amount="21.1" CurrencyCode="USD" /><Tax TaxCode="XF" Amount="4.5" CurrencyCode="USD" /><Tax TaxCode="YQ" Amount="80" CurrencyCode="USD" /></Taxes><TPA_Extension><Surcharges><Surcharge FareChargeCode="" FareChargeAmount="0.00" /></Surcharges></TPA_Extension></PassengerFare></PTC_FareBreakdown></PTC_FareBreakdowns></AirItineraryPricingInfo></PricedItinerary></PricedItineraries><IssueOfficeInfo OfficeID=""/></KIU_AirPriceRS>*/

        $price = [
            'baseFare'      => 0,
            'currencyCode'  => '',
            'taxes'         => [],
            'totalTaxes'    => 0,
            'totalFare'     => 0,
            'currencyCode'  => '',
        ];

        $pricedItineraries = $apiResponse['PricedItineraries']['PricedItinerary'];
        if (isset($pricedItineraries['AirItineraryPricingInfo'])) {
            $airItineraryPricingInfo = $pricedItineraries['AirItineraryPricingInfo'];
            if (isset($airItineraryPricingInfo['ItinTotalFare'])) {
                $irinTotalFare = $airItineraryPricingInfo['ItinTotalFare'];
                // get baseFare
                if (isset($irinTotalFare['BaseFare'])) {
                    $price['baseFare'] = $irinTotalFare['BaseFare']['@attributes']['Amount'];
                    $price['currencyCode'] = $irinTotalFare['BaseFare']['@attributes']['CurrencyCode'];
                }

                // get taxes
                if (isset($irinTotalFare['Taxes'])) {
                    $taxes = $irinTotalFare['Taxes']['Tax'];
                    if (isset($taxes['@attributes'])) {
                        $taxes = [$taxes];
                    }
                    foreach ($taxes as $tax) {
                        $price['taxes'][] = [
                            'taxCode'       => $tax['@attributes']['TaxCode'],
                            'amount'        => $tax['@attributes']['Amount'],
                            'currencyCode'  => $tax['@attributes']['CurrencyCode'],
                        ];
                        $price['totalTaxes'] += $tax['@attributes']['Amount'];
                    }
                }

                // get totalFare
                if (isset($irinTotalFare['TotalFare'])) {
                    $price['totalFare'] = $irinTotalFare['TotalFare']['@attributes']['Amount'];
                }

            }
        }

        return $price;
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
        $result = $this->formatFlights($response['response']['OriginDestinationInformation']);
        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'flights'               => $result['flights'],
            'depurateDate'          => $result['depurateDate'],
            'originLocation'        => $result['originLocation'],
            'destinationLocation'   => $result['destinationLocation'],
            'response'              => $response['response']['OriginDestinationInformation']
        ];
    }

    public function getFlightPrice ($depurateDateTime, $arrivalDateTime, $flightNumber, $resBookDesigCode, $originLocation, $destinationLocation, $airlineCode, $adults = 1, $children = 1) {
        // Create the xml object
        $this->createKIU_AirPriceRQObject();
        // add AirItinerary xml object to xml
        $this->createAirItineraryXmlObject($depurateDateTime, $arrivalDateTime, $flightNumber, $resBookDesigCode, $originLocation, $destinationLocation, $airlineCode);
        // add TravelerInfoSummary xml object to xml
        $this->createTravelerInfoSummaryXmlObject($adults, $children);
        // POST
        $response = $this->POST();
        // format price data
        $result = $this->formatFlightPrice($response['response']);

        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'price'                 => $result
        ];
    }
}