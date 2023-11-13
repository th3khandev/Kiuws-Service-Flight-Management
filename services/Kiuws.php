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
        '11005'     => 'Error at parameter DepartureDateTime.',
        '22030'     => 'ERROR EN COTIZACION'
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
        $source = $pos->addChild('Source');
        // add attributes to source
        $source->addAttribute('AgentSine', $this->agentSine);
        $source->addAttribute('TerminalID', $this->termialID);
        $source->addAttribute('ISOCountry', 'US');
        $source->addAttribute('ISOCurrency', 'USD');

        // add RequestorID xml object to POS
        if (!is_null($requestorID)) {
            $source->addChild('RequestorID Type="'. $requestorID .'"');
        }
        // add BookingChannel xml object to POS
        if (!is_null($bookingChannel)) {
            $source->addChild('BookingChannel Type="'. $bookingChannel .'"');
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

    private function createKIU_CancelRQ_XmlObject() {
        // Create the xml object
        $this->xml = new SimpleXMLElement('<KIU_CancelRQ EchoToken="1" TimeStamp="2021-06-24T11:54:36Z" Target="'. trim(ucfirst(strtolower($this->mode))) .'" Version="3.0" SequenceNmbr="1" PrimaryLangID="en-us"></KIU_CancelRQ>');
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

    private function createKIU_AirBookV2RQ_XmlObject () {
        // Create the xml object
        $this->xml = new SimpleXMLElement('<KIU_AirBookV2RQ xmlns:ns="http://www.opentravel.org/OTA/2003/05/common" xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" xmlns:sch="http://purl.oclc.org/dsdl/schematron" xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" EchoToken="PUNCANA'. date('Y') .'" TimeStamp="2021-08-10T12:00:00.0Z" Target="'. trim(ucfirst(strtolower($this->mode))) .'" Version="3.0" SequenceNmbr="1" PriceInd="true"></KIU_AirBookV2RQ>');
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

    private function createAirItineraryXmlObjectMultipleSegment ($flightSegments) {
        // add AirItinerary xml object to xml
        $airItinerary = $this->xml->addChild('AirItinerary');
        // add OriginDestinationOptions xml object to AirItinerary
        $originDestinationOptions = $airItinerary->addChild('OriginDestinationOptions');
        // add OriginDestinationOption xml object to OriginDestinationOptions
        $originDestinationOption = $originDestinationOptions->addChild('OriginDestinationOption');

        foreach ($flightSegments as $segment) {
            // add FlightSegment xml object to OriginDestinationOption
            $flightSegment = $originDestinationOption->addChild('FlightSegment');
            // add DepurateDateTime, ArrivalDateTime, FlightNumber, ResBookDesigCode attributtes to FlightSegment
            $flightSegment->addAttribute('DepartureDateTime', $segment['depurateDateTime']);
            $flightSegment->addAttribute('ArrivalDateTime', $segment['arrivalDateTime']);
            $flightSegment->addAttribute('FlightNumber', $segment['flightNumber']);
            $flightSegment->addAttribute('ResBookDesigCode', $segment['resBookDesig']);
            // add DepartureAirport xml object to FlightSegment
            $flightSegment->addChild('DepartureAirport LocationCode="'. $segment['origin'] .'"');
            // add ArrivalAirport xml object to FlightSegment
            $flightSegment->addChild('ArrivalAirport LocationCode="'. $segment['destination'] .'"');
            // add MarketingAirline xml object to FlightSegment
            $flightSegment->addChild('MarketingAirline Code="'. $segment['airlineCode'] .'"');
        }
    }

    private function createAirItineraryXmlToReservation () {
        // add AirItinerary xml object to xml
        $airItinerary = $this->xml->addChild('AirItinerary');
        // add OriginDestinationOptions xml object to AirItinerary
        $originDestinationOptions = $airItinerary->addChild('OriginDestinationOptions');
        // add OriginDestinationOption xml object to OriginDestinationOptions
        $originDestinationOption = $originDestinationOptions->addChild('OriginDestinationOption');

        return $originDestinationOption;
    }

    private function addSegmentToOriginDestinationOptionXmlObject ($originDestinationOption, $depurateDateTime, $flightNumber, $resBookDesigCode, $departureAirport, $arrivalAirport, $airlineCode, $arrivalDateTime = null, $segmentRPH = 1) {
        // add FlightSegment xml object to OriginDestinationOption
        $flightSegment = $originDestinationOption->addChild('FlightSegment');
        // add DepurateDateTime, ArrivalDateTime, FlightNumber, ResBookDesigCode attributtes to FlightSegment
        $flightSegment->addAttribute('DepartureDateTime', $depurateDateTime);
        $flightSegment->addAttribute('FlightNumber', $flightNumber);
        $flightSegment->addAttribute('ResBookDesigCode', $resBookDesigCode);
        $flightSegment->addAttribute('SegmentRPH', $segmentRPH);
        // add DepartureAirport xml object to FlightSegment
        $flightSegment->addChild('DepartureAirport LocationCode="'. $departureAirport .'"');
        // add ArrivalAirport xml object to FlightSegment
        $flightSegment->addChild('ArrivalAirport LocationCode="'. $arrivalAirport .'"');
        // add MarketingAirline xml object to FlightSegment
        $flightSegment->addChild('MarketingAirline Code="'. $airlineCode .'"');
        // add ArrivalDateTime xml object to FlightSegment
        if (!is_null($arrivalDateTime)) {
            $flightSegment->addAttribute('ArrivalDateTime', $arrivalDateTime);
        }
    }

    private function addAirTravelerToTravelerInfoXmlObject ($travelerInfoXml, $ptc, $name, $lastName, $documentType, $documentNumber, $phoneContryCode, $phoneAreaCode, $phoneNumber, $email, $birthDate, $refNumber) {
        // add AirTraveler xml object to TravelerInfo
        $airTraveler = $travelerInfoXml->addChild('AirTraveler');
        // add PersonName xml object to AirTraveler
        $personName = $airTraveler->addChild('PersonName');
        // add attributes to personName 
        $personName->addAttribute('PTC', $ptc);
        $personName->addAttribute('BirthDate', $birthDate);
        // add GivenName xml object to PersonName
        $personName->addChild('GivenName', strtoupper($name));
        // add Surname xml object to PersonName
        $personName->addChild('Surname', strtoupper($lastName));
        // add Document xml object to AirTraveler
        $airTraveler->addChild('Document DocType="'. $documentType .'" DocID="'. $documentNumber .'"');
        // add Telephone xml object to AirTraveler
        $airTraveler->addChild('Telephone CountryAccessCode="'. $phoneContryCode .'" AreaCityCode="'. $phoneAreaCode .'" PhoneNumber="'. $phoneNumber .'"');
        // add Email xml object to AirTraveler
        $airTraveler->addChild('Email', $email);
        // add TravelerRefNumber xml object to AirTraveler
        $airTraveler->addChild('TravelerRefNumber RPH="'. $refNumber .'"');
    }

    private function createTravelerInfoSummaryXmlObject($adults = 1, $children = 0, $inf = 0) {
        // add TravelerInfoSummary to xml
        $travelerInfoSummary = $this->xml->addChild('TravelerInfoSummary');
        // add AirTravelerAvail to TravelerInfoSummary
        $airTravelerAvail = $travelerInfoSummary->addChild('AirTravelerAvail');
        // add PassengerTypeQuantity to AirTravelerAvail
        $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
        // add Code, Quantity attributtes to PassengerTypeQuantity
        $passengerTypeQuantity->addAttribute('Code', 'ADT');
        $passengerTypeQuantity->addAttribute('Quantity', $adults);

        if ($inf > 0) {
            // add PassengerTypeQuantity to AirTravelerAvail
            $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
            // add Code, Quantity attributtes to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Code', 'INF');
            $passengerTypeQuantity->addAttribute('Quantity', $inf);
        }
        
        if ($children > 0) {
            // add PassengerTypeQuantity to AirTravelerAvail
            $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
            // add Code, Quantity attributtes to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Code', 'CNN');
            $passengerTypeQuantity->addAttribute('Quantity', $children);
        }
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

    private function createTravelerInfoSummary ($adults = 1, $children = 0, $inf = 0) {
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
        
        if ($children > 0) {
            // add PassengerTypeQuantity xml object to AirTravelerAvail
            $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
            // add Code Attribute to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Code', 'CHD');
            // add Quantity Attribute to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Quantity', $children);
        }

        if  ($inf > 0) {
            // add PassengerTypeQuantity xml object to AirTravelerAvail
            $passengerTypeQuantity = $airTravelerAvail->addChild('PassengerTypeQuantity');
            // add Code Attribute to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Code', 'INF');
            // add Quantity Attribute to PassengerTypeQuantity
            $passengerTypeQuantity->addAttribute('Quantity', $inf);
        }
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
                'response'  => $response,
            ];
        }

        // Process the response
        return [
            'status'    => 'success',
            'message'   => '',
            'response'   => $response
        ];
    }

    private function formatFlights ($apiResponse, $tripType = 1) {
        $flights = [];
        $returnFlights = [];

        $depurateDate = null;
        $originLocation = null;
        $destinationLocation = null;

        if ($tripType == 1) {
            $depurateDate = $apiResponse['DepartureDateTime'];
            $originLocation = $apiResponse['OriginLocation'];
            $destinationLocation = $apiResponse['DestinationLocation'];
            $originDestinationOptions = $apiResponse['OriginDestinationOptions']['OriginDestinationOption'];
            if (isset($originDestinationOptions['FlightSegment'])) {
                $originDestinationOptions = [$originDestinationOptions];
            }
        } else if ($tripType == 2) {
            $originDestinationInformation = $apiResponse[0];
            $depurateDate = $originDestinationInformation['DepartureDateTime'];
            $originLocation = $originDestinationInformation['OriginLocation'];
            $destinationLocation = $originDestinationInformation['DestinationLocation'];
            $originDestinationOptions = $originDestinationInformation['OriginDestinationOptions']['OriginDestinationOption'];
        }



        // Forearch flights
        foreach ($originDestinationOptions as $originDestinationOption) {
            $flight = [
                'flightSegment' => [],
                'depurateDate'  => '',
                'arrivalDate'   => '',
                'duration'      => '',
                'stops'         => 0,
                'id'            => '',
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

                // Get stops
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
                    if (count($bookingClassAvail) == 1) {
                        $_bookingClass = $bookingClassAvail['@attributes'];
                        $_bookingClassAvail = [
                            'resBookDesigCode'      => $_bookingClass['ResBookDesigCode'],
                            'resBookDesigQuantity'  => $_bookingClass['ResBookDesigQuantity'],
                            'rph'                   => $_bookingClass['RPH'],
                        ];
                    } else {
                        $_bookingClassAvail = [
                            'resBookDesigCode'      => $bookingClassAvail['ResBookDesigCode'],
                            'resBookDesigQuantity'  => $bookingClassAvail['ResBookDesigQuantity'],
                            'rph'                   => $bookingClassAvail['RPH'],
                        ];
                    }
                    array_push($_flightSegment['bookingClassAvail'], $_bookingClassAvail);
                }

                array_push($flight['flightSegment'], $_flightSegment);

                // modify id flight
                $flight['id'] .= $flightSegment['@attributes']['FlightNumber'];
            }
            // get depurate time flight
            $flight['depurateDate'] = $flight['flightSegment'][0]['departureDateTime'];
            // get arrival time flight
            $flight['arrivalDate'] = $flight['flightSegment'][count($flight['flightSegment']) - 1]['arrivalDateTime'];
            // get duration time flight (ArrivalDateTime - DepartureDateTime)
            $flight['duration'] = date('H:i', strtotime($flight['arrivalDate']) - strtotime($flight['depurateDate']));
            array_push($flights, $flight);
        }

        // if tripType is roundtrip, set returnFlights
        $returnFlightsDestinationOptions = $apiResponse[1]['OriginDestinationOptions']['OriginDestinationOption'];
        foreach ($returnFlightsDestinationOptions as $originDestinationOption) {
            $flight = [
                'flightSegment' => [],
                'depurateDate'  => '',
                'arrivalDate'   => '',
                'duration'      => '',
                'stops'         => 0,
                'id'            => '',
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

                // Get stops
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
                    if (count($bookingClassAvail) == 1) {
                        $_bookingClass = $bookingClassAvail['@attributes'];
                        $_bookingClassAvail = [
                            'resBookDesigCode'      => $_bookingClass['ResBookDesigCode'],
                            'resBookDesigQuantity'  => $_bookingClass['ResBookDesigQuantity'],
                            'rph'                   => $_bookingClass['RPH'],
                        ];
                    } else {
                        $_bookingClassAvail = [
                            'resBookDesigCode'      => $bookingClassAvail['ResBookDesigCode'],
                            'resBookDesigQuantity'  => $bookingClassAvail['ResBookDesigQuantity'],
                            'rph'                   => $bookingClassAvail['RPH'],
                        ];
                    }
                    array_push($_flightSegment['bookingClassAvail'], $_bookingClassAvail);
                }

                array_push($flight['flightSegment'], $_flightSegment);

                // modify id flight
                $flight['id'] .= $flightSegment['@attributes']['FlightNumber'];
            }

            // get depurate time flight
            $flight['depurateDate'] = $flight['flightSegment'][0]['departureDateTime'];
            // get arrival time flight
            $flight['arrivalDate'] = $flight['flightSegment'][count($flight['flightSegment']) - 1]['arrivalDateTime'];
            // get duration time flight (ArrivalDateTime - DepartureDateTime)
            $flight['duration'] = date('H:i', strtotime($flight['arrivalDate']) - strtotime($flight['depurateDate']));
            array_push($returnFlights, $flight);                
        }


        return [
            'depurateDate'          => $depurateDate,
            'originLocation'        => $originLocation,
            'destinationLocation'   => $destinationLocation,
            'flights'               => $flights,
            'returnFlights'         => $returnFlights,
        ];
    }

    private function formatFlightPrice ($apiResponse) {

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
                    $price['baseFare'] = (float) $irinTotalFare['BaseFare']['@attributes']['Amount'];
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
                        $price['totalTaxes'] += (float) $tax['@attributes']['Amount'];
                    }
                }

                // get totalFare
                if (isset($irinTotalFare['TotalFare'])) {
                    $price['totalFare'] = (float) $irinTotalFare['TotalFare']['@attributes']['Amount'];
                }

            }
        }

        return $price;
    }

    public function getAvailabilityFlights ($depurateDate, $originLocation, $destinationLocation, $adults = 1, $children = 0, $inf = 0,  $returnDate = null, $tripType = 1, $maxStopQuantity = 4) {
        // Create the xml object
        $this->createKIU_AirAvailRQXmlObject();
        // add OriginDestinationInformation xml object to xml
        $this->createOriginDestinationInformation($depurateDate, $originLocation, $destinationLocation);

        if ($tripType == 2) {
            // add OriginDestinationInformation xml object to xml
            $this->createOriginDestinationInformation($returnDate, $destinationLocation, $originLocation);
        }

        // add TravelPreferences xml object to xml
        $this->createTravelPreferences($maxStopQuantity);
        // add TravelerInfoSummary xml object to xml
        $this->createTravelerInfoSummary($adults, $children, $inf);
        // POST
        $response = $this->POST();
        if ($response['status'] === 'error') {
            return $response + ['xml' => $this->xml->asXML()];
        }
        
        // Format response
        $result = $this->formatFlights($response['response']['OriginDestinationInformation'], $tripType);
        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'flights'               => $result['flights'],
            'returnFlights'         => $result['returnFlights'],
            'depurateDate'          => $result['depurateDate'],
            'originLocation'        => $result['originLocation'],
            'destinationLocation'   => $result['destinationLocation'],
            'xml'                   => $this->xml->asXML(),
            'response'              => $response['response'],
        ];
    }

    public function getFlightPrice ($depurateDateTime, $arrivalDateTime, $flightNumber, $resBookDesigCode, $originLocation, $destinationLocation, $airlineCode, $adults = 1, $children = 0, $inf = 0) {
        // Create the xml object
        $this->createKIU_AirPriceRQObject();
        // add AirItinerary xml object to xml
        $this->createAirItineraryXmlObject($depurateDateTime, $arrivalDateTime, $flightNumber, $resBookDesigCode, $originLocation, $destinationLocation, $airlineCode);
        // add TravelerInfoSummary xml object to xml
        $this->createTravelerInfoSummaryXmlObject($adults, $children, $inf);
        // POST
        $response = $this->POST();
        if ($response['status'] === 'error') {
            return $response;
        }
        // format price data
        $result = $this->formatFlightPrice($response['response']);

        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'price'                 => $result,
            'response'              => $response['response'],
            'xml'                   => $this->xml->asXML()
        ];
    }

    public function getFlightPriceMultipleSegments ($flightSegments, $adults = 1, $children = 0, $inf = 0) {
        // Create the xml object
        $this->createKIU_AirPriceRQObject();
        // add AirItinerary xml object to xml
        $this->createAirItineraryXmlObjectMultipleSegment($flightSegments);
        // add TravelerInfoSummary xml object to xml
        $this->createTravelerInfoSummaryXmlObject($adults, $children, $inf);
        // POST
        $response = $this->POST();
        if ($response['status'] === 'error') {
            return $response;
        }
        // format price data
        $result = $this->formatFlightPrice($response['response']);

        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'price'                 => $result,
            'response'              => $response['response']
        ];
    }

    private function formatReservationResponse($responseApi) {
        $bookingId = $responseApi['BookingReferenceID']['@attributes']['ID'];
        $ticketTimeLimit = $responseApi['BookingReferenceID']['@attributes']['TicketTimeLimit'];
        $priceInfoResponse = $responseApi['PricingInfo'];
        return [
            'bookingId'         => $bookingId,
            'ticketTimeLimit'   => $ticketTimeLimit,
            'priceInfoResponse' => $priceInfoResponse,
        ];
    }

    public function createReservation ($request) {
        // create xml object
        $this->createKIU_AirBookV2RQ_XmlObject();
        // add AirItinerary xml object to xml
        $originDestinationOption = $this->createAirItineraryXmlToReservation();
        // add flight segments
        foreach ($request['segment'] as $segment) {
            $departureDateTime = str_replace(' ', 'T', $segment['departureDateTime']);
            $this->addSegmentToOriginDestinationOptionXmlObject($originDestinationOption, $departureDateTime, $segment['flightNumber'], $segment['resBookDesig'], $segment['departureAirport'], $segment['arrivalAirport'], $segment['airlineCode']);
        }
        // add TravelerInfo to xml
        $travelerInfo = $this->xml->addChild('TravelerInfo');

        // order passengers first all adults, then all infants and finally all children
        $passengers = [];
        foreach ($request['passengers'] as $passenger) {
            if ($passenger['type'] == 'adult') {
                array_push($passengers, $passenger);
            }
        }
        foreach ($request['passengers'] as $passenger) {
            if ($passenger['type'] == 'inf') {
                array_push($passengers, $passenger);
            }
        }
        foreach ($request['passengers'] as $passenger) {
            if ($passenger['type'] == 'child') {
                array_push($passengers, $passenger);
            }
        }

        // add passenger info to TravelerInfo
        foreach ($passengers as $key => $passenger) {

            if ($passenger['type'] == 'adult') {
                $ptc = 'ADT';
            } else if ($passenger['type'] == 'child') {
                $ptc = 'CHD';
            } else if ($passenger['type'] == 'inf') {
                $ptc = 'INF';
            } else {
                $ptc = '';
            }

            $this->addAirTravelerToTravelerInfoXmlObject($travelerInfo, $ptc, $passenger['name'], $passenger['lastName'], $passenger['documentType'], $passenger['documentNumber'], $passenger['phoneCountryCode'], $passenger['phoneCountryCode'], $passenger['phoneNumber'], $passenger['email'], $passenger['birthDate'], $key + 1);
        }
        // create date ticket limit ($request['depurateDate'] - 2 hour)
        $ticketLimit = date('Y-m-d H:i:s', strtotime($request['depurateDate']) - 7200);
        $ticketLimit = str_replace(' ', 'T', $ticketLimit);
        // add Ticketing xml object to xml
        $this->xml->addChild('Ticketing');
        // add attributes to Ticketing
        $this->xml->Ticketing->addAttribute('CancelOnExpiryInd', 'false');
        $this->xml->Ticketing->addAttribute('TicketTimeLimit', $ticketLimit);
        $this->xml->Ticketing->addAttribute('TimeLimitCity', 'SDQ');

        // POST
        $response = $this->POST();

        if ($response['status'] === 'error') {
            $response['xml'] = $this->xml->asXML();
            return $response;
        }

        $result = $this->formatReservationResponse($response['response']);
        return [
            'status'                => $response['status'],
            'message'               => $response['message'],
            'bookingId'             => $result['bookingId'],
            'ticketTimeLimit'       => $result['ticketTimeLimit'],
            'priceInfoResponse'     => $result['priceInfoResponse'],
            'response'              => $response['response']
        ];
    }

    public function cancelReservation ($booking_id) {
        // create xml object
        $this->createKIU_CancelRQ_XmlObject();
        // add UniqueID xml object to xml
        $uniqueID = $this->xml->addChild('UniqueID');
        // add attributes to UniqueID
        $uniqueID->addAttribute('ID', $booking_id);
        $uniqueID->addAttribute('Type', '14');
        // POST
        $attemp = 1;
        $max_attemp = 3;

        $response = [];

        while ($attemp <= $max_attemp) {
            $response = $this->POST();
            if ($response['status'] == 'success') {
                break;
            }
            // validate if error cotain "URL error 28" text
            if ($response['status'] == 'error' && strpos($response['message'], 'URL error 28') === false) {
                $attemp++;
                continue;
            }
            break;
        }
        if ($response['status'] === 'error') {
            return $response;
        }
        // validate if response has key 'success'
        if (!isset($response['response']['Success'])) {
            return [
                'status'    => 'error',
                'message'   => 'Error al cancelar la reservación',
                'response'  => $response['response']
            ];
        }

        return [
            'status'    => 'success',
            'message'   => 'Cancelado con éxito',
            'response'  => $response['response']
        ];
    }
}