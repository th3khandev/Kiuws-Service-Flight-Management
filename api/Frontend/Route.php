<?php

namespace Kiuws_Service_Flight_Management\Api\Frontend;

use Kiuws_Service_Flight_Management\DB\FlightContactModel;
use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use Kiuws_Service_Flight_Management\DB\FlightPassengerModel;
use Kiuws_Service_Flight_Management\DB\FlightSegmentModel;
use Kiuws_Service_Flight_Management\DB\FlightTaxModel;
use Kiuws_Service_Flight_Management\Services\Kiuws;
use Kiuws_Service_Flight_Management\Services\OpenFlightsOrg;
use WP_REST_Controller;
use WP_REST_Server;

class Route extends WP_REST_Controller
{

    protected $namespace;
    private $kiuwsService;

    public function __construct()
    {
        $this->namespace = 'kiuws-flight-management/v1';

        // Get configuration from options
        $base_url = get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url');
        $agent_sine = get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine');
        $terminal_id = get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id');
        $user = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user'));
        $password = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password'));
        $mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode');

        $this->kiuwsService = new Kiuws($base_url, $agent_sine, $terminal_id, $user, $password, $mode);
    }

    /**
     * Register routes
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            'get-airport-codes',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_airport_codes'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            'get-flight-available',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_flight_available'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            'get-flight-price',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'get_flight_price'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            'create-reservation',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'create_reservation'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );
    }

    /**
     * Get route access
     */
    public function get_route_access($request)
    {
        return true;
    }

    /**
     * Get airport codes
     */
    public function get_airport_codes($request)
    {
        $params = $request->get_params();
        $search = $params['search'] ?? '';

        $open_flight_org = new OpenFlightsOrg();
        $response = $open_flight_org->getAirports($search);

        return rest_ensure_response($response);
    }

    /**
     * Get flight available
     */
    public function get_flight_available($request) {
        $params = $request->get_params();
        $origin = $params['origin'];
        $destination = $params['destination'];
        $departure_date = $params['depurate_date'];
        $adults = $params['adults'];
        $children = $params['children'];

        $attemp = 1;
        $max_attemp = 3;

        while ($attemp <= $max_attemp) {
            $response = $this->kiuwsService->getAvailabilityFlights($departure_date, $origin, $destination, $adults, $children);
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

        return rest_ensure_response($response);
    }

    /**
     * Get flight price
     */
    public function get_flight_price ($request) {
        $params = $request->get_params();

        $flight_segments = $params['flight_segments'];
        $flight_segments_validate = [];
        $adults = 1;
        $children = 0;

        foreach($flight_segments as $flight_segment) {
            $depurate_date_time = $flight_segment['depurateDateTime'];
            $arrival_date_time = $flight_segment['arrivalDateTime'];
            $origin = $flight_segment['origin'];
            $destination = $flight_segment['destination'];
            $adults = $flight_segment['adults'];
            $children = $flight_segment['children'];
            $flight_number = $flight_segment['flightNumber'];
            $resBookDesig = $flight_segment['resBookDesig'];
            $airlineCode = $flight_segment['airlineCode'];

            $listResBookDesig = explode(',', $resBookDesig);
            foreach ($listResBookDesig as $value) {
                $response = $this->kiuwsService->getFlightPrice($depurate_date_time, $arrival_date_time, $flight_number, trim($value), $origin, $destination, $airlineCode, $adults, $children);
                if ($response['status'] == 'success') {
                    // add booking code to response
                    $flight_segments_validate[] = [
                        'depurateDateTime' => $depurate_date_time,
                        'arrivalDateTime' => $arrival_date_time,
                        'origin' => $origin,
                        'destination' => $destination,
                        'adults' => $adults,
                        'children' => $children,
                        'flightNumber' => $flight_number,
                        'resBookDesig' => trim($value),
                        'airlineCode' => $airlineCode,
                    ];
                    break;
                }
            }
        }

        if (count($flight_segments) != count($flight_segments_validate)) {
            return rest_ensure_response([
                'status' => 'error',
                'message' => 'No se completo la cotización de todos los vuelos.'
            ]);
        }


        $response = $this->kiuwsService->getFlightPriceMultipleSegments($flight_segments_validate, $adults, $children);
        $response['flight_segments'] = $flight_segments_validate;
        return rest_ensure_response($response);
    }

    public function create_reservation($request) {
        $params = $request->get_params();

        $attemp = 1;
        $max_attemp = 3;

        while ($attemp <= $max_attemp) {
            $response = $this->kiuwsService->createReservation($params);
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

        if ($response['status'] == 'error') {
            return rest_ensure_response([
                'status' => 'error',
                'message' => 'No se pudo crear la reserva.',
                'response' => $response
            ]);
        }

        // save reservation in database
        $flight = new FlightManagementModel();
        $flight->departure_date_time = $params['depurateDate'];
        $flight->arrival_date_time = $params['arrivalDate'];
        $flight->origin_airport_code = $params['origin'];
        $flight->origin = $params['originAirport'];
        $flight->destination_airport_code = $params['destination'];
        $flight->destination = $params['destinationAirport'];
        $flight->duration = $params['duration'];
        $flight->stops = $params['stops'];
        $flight->adults = $params['adults'];
        $flight->children = $params['children'];
        $flight->base_fare = $params['baseFare'];
        $flight->total_taxes = $params['totalTaxes'];
        $flight->total = $params['total'];
        $flight->currency_code = $params['currencyCode'];
        $flight->status = FlightManagementModel::STATUS_BOOKED;
        $flight->booking_id = $response['bookingId'];
        $flight->ticket_time_limit = $response['ticketTimeLimit'];
        $flight->price_info_response = json_encode($response['priceInfoResponse'], JSON_UNESCAPED_UNICODE);
        $flight->save();

        // save taxes in database
        foreach ($params['taxes'] as $tax) {
            $flight_tax = new FlightTaxModel();
            $flight_tax->flight_id = $flight->id;
            $flight_tax->tax_code = $tax['taxCode'];
            $flight_tax->amount = $tax['amount'];
            $flight_tax->currency_code = $tax['currencyCode'];
            $flight_tax->save();
        }

        // save segments in database
        foreach ($params['segment'] as $segment) {
            $flight_segment = new FlightSegmentModel();
            $flight_segment->flight_id = $flight->id;
            $flight_segment->departure_date_time = $segment['departureDateTime'];
            $flight_segment->arrival_date_time = $segment['arrivalDateTime'];
            $flight_segment->origin_airport_code = $segment['departureAirport'];
            $flight_segment->destination_airport_code = $segment['arrivalAirport'];
            $flight_segment->duration = $segment['duration'];
            $flight_segment->airline_code = $segment['airlineCode'];
            $flight_segment->airline_name = $segment['airlineName'];
            $flight_segment->flight_number = $segment['flightNumber'];
            $flight_segment->res_book_desig = $segment['resBookDesig'];
            $flight_segment->save();
        }

        // save contacts in database
        $flight_contact = new FlightContactModel();
        $flight_contact->flight_id = $flight->id;
        $flight_contact->name = $params['contactInfo']['name'];
        $flight_contact->last_name = $params['contactInfo']['lastName'];
        $flight_contact->email = $params['contactInfo']['email'];
        $flight_contact->phone_country_code = $params['contactInfo']['phoneCountryCode'];
        $flight_contact->phone_number = $params['contactInfo']['phoneNumber'];
        $flight_contact->save();

        // save passengers in database
        foreach ($params['passengers'] as $passenger) {
            $flight_passenger = new FlightPassengerModel();
            $flight_passenger->flight_id = $flight->id;
            $flight_passenger->type = $passenger['type'];
            $flight_passenger->name = $passenger['name'];
            $flight_passenger->last_name = $passenger['lastName'];
            $flight_passenger->email = $passenger['email'];
            $flight_passenger->gender = $passenger['gender'];
            $flight_passenger->birth_date = $passenger['birthDate'];
            $flight_passenger->document_type = $passenger['documentType'];
            $flight_passenger->document_number = $passenger['documentNumber'];
            $flight_passenger->phone_country_code = $passenger['phoneCountryCode'];
            $flight_passenger->phone_number = $passenger['phoneNumber'];
            $flight_passenger->save();
        }

        $response['flight_id'] = $flight->id;
        $response['reservation_status'] = $flight->status;
        $response['message'] = 'Reserva creada correctamente.';
        return rest_ensure_response($response);
    }
}
