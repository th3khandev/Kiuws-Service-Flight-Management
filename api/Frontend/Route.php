<?php

namespace Kiuws_Service_Flight_Management\Api\Frontend;

use Exception;
use Kiuws_Service_Flight_Management\DB\FlightContactModel;
use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use Kiuws_Service_Flight_Management\DB\FlightPassengerModel;
use Kiuws_Service_Flight_Management\DB\FlightPaymentInfoModel;
use Kiuws_Service_Flight_Management\DB\FlightSegmentModel;
use Kiuws_Service_Flight_Management\DB\FlightTaxModel;
use Kiuws_Service_Flight_Management\Services\Kiuws;
use Kiuws_Service_Flight_Management\Services\OpenFlightsOrg;
use Stripe\StripeClient;
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
        // set fee value
        $fee = get_option(FLIGHT_MANAGEMENT_PREFIX . 'fee');

        $this->kiuwsService = new Kiuws($base_url, $agent_sine, $terminal_id, $user, $password, $mode, $fee);
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

        register_rest_route(
            $this->namespace,
            'process-payment',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'process_payment'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            'test-email-reservation',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'testEmail'],
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
        $inf = $params['inf'];
        $return_date = $params['return_date'] ?? null;
        $trip_type = $params['trip_type'] ?? 1;

        $attemp = 1;
        $max_attemp = 3;

        while ($attemp <= $max_attemp) {
            $response = $this->kiuwsService->getAvailabilityFlights($departure_date, $origin, $destination, $adults, $children, $inf, $return_date, $trip_type);
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

        $departure_flight_segments = $params['departure_flight_segments'];
        $return_flight_segments = $params['return_flight_segments'] ?? [];
        $departure_flight_segments_validate = [];
        $return_flight_segments_validate = [];
        $adults = 1;
        $children = 0;
        $inf = 0;

        // validate departure segments
        foreach($departure_flight_segments as $flight_segment) {
            $depurate_date_time = $flight_segment['depurateDateTime'];
            $arrival_date_time = $flight_segment['arrivalDateTime'];
            $origin = $flight_segment['origin'];
            $destination = $flight_segment['destination'];
            $adults = $flight_segment['adults'];
            $children = $flight_segment['children'];
            $inf = $flight_segment['inf'];
            $flight_number = $flight_segment['flightNumber'];
            $resBookDesig = $flight_segment['resBookDesig'];
            $airlineCode = $flight_segment['airlineCode'];

            $listResBookDesig = explode(',', $resBookDesig);
            foreach ($listResBookDesig as $value) {
                $response = $this->kiuwsService->getFlightPrice($depurate_date_time, $arrival_date_time, $flight_number, trim($value), $origin, $destination, $airlineCode, $adults, $children, $inf);
                if ($response['status'] == 'success') {
                    // add booking code to response
                    $departure_flight_segments_validate[] = [
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

        if (count($departure_flight_segments) != count($departure_flight_segments_validate)) {
            return rest_ensure_response([
                'status' => 'error',
                'message' => 'No se completo la cotización de todos los vuelos.'
            ]);
        }

        // validate return segments
        foreach($return_flight_segments as $flight_segment) {
            $depurate_date_time = $flight_segment['depurateDateTime'];
            $arrival_date_time = $flight_segment['arrivalDateTime'];
            $origin = $flight_segment['origin'];
            $destination = $flight_segment['destination'];
            $adults = $flight_segment['adults'];
            $children = $flight_segment['children'];
            $inf = $flight_segment['inf'];
            $flight_number = $flight_segment['flightNumber'];
            $resBookDesig = $flight_segment['resBookDesig'];
            $airlineCode = $flight_segment['airlineCode'];

            $listResBookDesig = explode(',', $resBookDesig);
            foreach ($listResBookDesig as $value) {
                $response = $this->kiuwsService->getFlightPrice($depurate_date_time, $arrival_date_time, $flight_number, trim($value), $origin, $destination, $airlineCode, $adults, $children, $inf);
                if ($response['status'] == 'success') {
                    // add booking code to response
                    $return_flight_segments_validate[] = [
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

        if (count($return_flight_segments) != count($return_flight_segments_validate)) {
            return rest_ensure_response([
                'status' => 'error',
                'message' => 'No se completo la cotización de todos los vuelos.'
            ]);
        }


        $response = $this->kiuwsService->getFlightPriceMultipleSegments($departure_flight_segments_validate, $return_flight_segments_validate, $adults, $children, $inf);
        $response['departure_flight_segments'] = $departure_flight_segments_validate;
        $response['return_flight_segments'] = $return_flight_segments_validate;
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
        $flight->inf = $params['inf'];
        $flight->base_fare = $params['baseFare'];
        $flight->total_taxes = $params['totalTaxes'];
        $flight->extra = $params['fee'];
        $flight->total = $params['total'];
        $flight->currency_code = 'USD';
        $flight->status = FlightManagementModel::STATUS_BOOKED;
        $flight->booking_id = $response['bookingId'];
        $flight->ticket_time_limit = $response['ticketTimeLimit'];
        $flight->price_info_response = json_encode($response['priceInfoResponse'], JSON_UNESCAPED_UNICODE);
        $flight->trip_type = $params['tripType'];
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

        $flight_segments = [];

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

            $flight_segments[] = $flight_segment;
        }

        // save return flight segments
        if (isset($params['returnSegments']) && count($params['returnSegments']) > 0) {
            foreach ($params['returnSegments'] as $segment) {
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

                $flight_segments[] = $flight_segment;
            }

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

        // send email to user admin
        $this->sendEmailToAdmin($flight, $flight_contact);

        // send email to user
        $this->sendEmailToUser($flight_contact->email, $flight, $flight_contact, $flight_segments);

        return rest_ensure_response($response);
    }

    public function process_payment ($request) {
        $params = $request->get_params();

        // get stripe public key
        $stripe_mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode');
        $stripe_private_key = get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_' . $stripe_mode . '_private_key');

        // create stripe client
        $stripe = new StripeClient($stripe_private_key);
        $customer = $stripe->customers->create([
            'email' => $params['card_email'],
            'name' => $params['card_name'],
        ]);

        $paymentData = [
            'amount'        => (int) ($params['amount'] * 100),
            'currency'      => strtolower($params['currency_code']),
            'description'   => 'Pago de reservacion de vuelo ID: ' . $params['flight_booking_id'],
            'customer'      => $customer->id,
            'source'        => $params['card_token'],
        ];

        try {
            $stripe->charges->create($paymentData);
        } catch (\Stripe\Exception\CardException $e) {
            // El pago falló debido a un problema con la tarjeta
            wp_send_json(['success' => false, 'message' => $e->getError()->message]);
        } catch (\Stripe\Exception\RateLimitException $e) {
            wp_send_json(['success' => false, 'message' => 'Error de límite de tasa']);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            wp_send_json(['success' => false, 'message' => 'Error de solicitud inválida']);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            wp_send_json(['success' => false, 'message' => 'Error de autenticación']);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            wp_send_json(['success' => false, 'message' => 'Error de conexión con la API']);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            wp_send_json(['success' => false, 'message' => 'Error de la API de Stripe']);
        } catch (Exception $e) {
            wp_send_json(['success' => false, 'message' => 'Error desconocido']);
        }

        // create payment info
        $flight_payment_info = new FlightPaymentInfoModel();
        $flight_payment_info->reference = $params['flight_booking_id'];
        $flight_payment_info->card_holder_name = $params['card_name'];
        $flight_payment_info->card_holder_document_number = $params['card_document_number'];
        $flight_payment_info->card_holder_email = $params['card_email'];
        $flight_payment_info->card_number = $params['card_number'];
        $flight_payment_info->currency = $params['currency_code'];
        $flight_payment_info->type = 'Stripe';
        $flight_payment_info->date = date('Y-m-d H:i:s');

        // get flight by booling id
        $flight_model = new FlightManagementModel();
        $flight = $flight_model->getFlightByBookingId($params['flight_booking_id']);

        // add flight id to payment model
        $flight_payment_info->flight_id = $flight->id;
        $flight_payment_info->save();

        // update flight status
        $flight->status = FlightManagementModel::STATUS_PAID;
        $flight->update();

        return [
            'success' => true,
            'message' => 'Pago realizado correctamente.',
        ];
    }

    private function sendEmailToAdmin ($flight, $flight_contact) {
        // send email to user admin
        $email_to = get_option(FLIGHT_MANAGEMENT_PREFIX . 'email_to');
        $email_to = trim($email_to);

        // get blogname
        $blogname = get_option('blogname');

        // get html template
        $html_template = file_get_contents(FLIGHT_MANAGEMENT_DIR . 'templates/emails/admin.php');

        // replate title in template by blogname
        $html_template = str_replace('{{title}}', $blogname, $html_template);

        // get blog logo url
        $logo_url = FLIGHT_MANAGEMENT_URL . 'assets/images/logo-puncana.jpg';

        // replace logo url in template
        $html_template = str_replace('{{logo}}', $logo_url, $html_template);

        // get url site
        $url_site = get_site_url();

        // replace url site in template
        $html_template = str_replace('{{site_url}}', $url_site, $html_template);

        // set main banner
        $main_banner = FLIGHT_MANAGEMENT_URL . 'assets/images/somos-la-familia-puncana.jpg';

        // replace main banner in template
        $html_template = str_replace('{{main_banner}}', $main_banner, $html_template);

        // set flight data in template
        $html_template = str_replace('{{departure_date}}', $flight->departure_date_time, $html_template);
        $html_template = str_replace('{{arrival_date}}', $flight->arrival_date_time, $html_template);
        $html_template = str_replace('{{origin}}', $flight->origin, $html_template);
        $html_template = str_replace('{{destination}}', $flight->destination, $html_template);
        $html_template = str_replace('{{adults}}', $flight->adults, $html_template);
        $html_template = str_replace('{{children}}', $flight->children, $html_template);
        $html_template = str_replace('{{inf}}', $flight->inf, $html_template);
        $html_template = str_replace('{{duration}}', $flight->duration, $html_template);
        $html_template = str_replace('{{stops}}', $flight->stops, $html_template);
        $trip_type = 'NO';
        if ($flight->trip_type == FlightManagementModel::TRIP_TYPE_ROUND_TRIP) {
            $trip_type = 'SI';
        }
        $html_template = str_replace('{{trip_type}}', $trip_type, $html_template);

        // add flight contact data
        $html_template = str_replace('{{contact_name}}', $flight_contact->name, $html_template);
        $html_template = str_replace('{{contact_last_name}}', $flight_contact->last_name, $html_template);
        $html_template = str_replace('{{contact_email}}', $flight_contact->email, $html_template);
        $html_template = str_replace('{{contact_phone_number}}', $flight_contact->phone_number, $html_template);

        // create flight_link 
        $flight_link = get_site_url() . '/wp-admin/admin.php?page=flight-management&booking_id=' . $flight->booking_id;
        
        // replce flight_link in template html
        $html_template = str_replace('{{flight_link}}', $flight_link, $html_template);

        // create puncana footer image
        $puncana_footer_image = FLIGHT_MANAGEMENT_URL . 'assets/images/puncana-footer.jpg';

        // replace image_footer in template html
        $html_template = str_replace('{{image_footer}}', $puncana_footer_image, $html_template);


        // set header response html not like JSON
        // header('Content-Type: text/html; charset=UTF-8');

        // send email
        wp_mail($email_to, 'Nueva reserva de vuelo', $html_template, [
            'Content-Type: text/html; charset=UTF-8',
        ]);
    }

    private function sendEmailToUser ($user_email, $flight, $flight_contact, $flight_segments) {
        // get blogname
        $blogname = get_option('blogname');

        // get html template
        $html_template = file_get_contents(FLIGHT_MANAGEMENT_DIR . 'templates/emails/user.php');

        // replate title in template by blogname
        $html_template = str_replace('{{title}}', $blogname, $html_template);

        // get blog logo url
        $logo_url = FLIGHT_MANAGEMENT_URL . 'assets/images/logo-puncana.jpg';

        // replace logo url in template
        $html_template = str_replace('{{logo}}', $logo_url, $html_template);

        // get url site
        $url_site = get_site_url();

        // replace url site in template
        $html_template = str_replace('{{site_url}}', $url_site, $html_template);

        // set main banner
        $main_banner = FLIGHT_MANAGEMENT_URL . 'assets/images/somos-la-familia-puncana.jpg';

        // replace main banner in template
        $html_template = str_replace('{{main_banner}}', $main_banner, $html_template);

        // set flight data in template
        $html_template = str_replace('{{id}}', $flight->booking_id, $html_template);
        $html_template = str_replace('{{departure_date}}', $flight->departure_date_time, $html_template);
        $html_template = str_replace('{{arrival_date}}', $flight->arrival_date_time, $html_template);
        $html_template = str_replace('{{origin}}', $flight->origin, $html_template);
        $html_template = str_replace('{{destination}}', $flight->destination, $html_template);
        $html_template = str_replace('{{adults}}', $flight->adults, $html_template);
        $html_template = str_replace('{{children}}', $flight->children, $html_template);
        $html_template = str_replace('{{inf}}', $flight->inf, $html_template);
        $html_template = str_replace('{{duration}}', $flight->duration, $html_template);
        $html_template = str_replace('{{stops}}', $flight->stops, $html_template);
        $trip_type = 'NO';
        if ($flight->trip_type == FlightManagementModel::TRIP_TYPE_ROUND_TRIP) {
            $trip_type = 'SI';
        }
        $html_template = str_replace('{{trip_type}}', $trip_type, $html_template);

        // add flight contact data
        $html_template = str_replace('{{contact_name}}', $flight_contact->name, $html_template);
        $html_template = str_replace('{{contact_last_name}}', $flight_contact->last_name, $html_template);
        $html_template = str_replace('{{contact_email}}', $flight_contact->email, $html_template);
        $html_template = str_replace('{{contact_phone_number}}', $flight_contact->phone_number, $html_template);

        // add payment data
        $html_template = str_replace('{{currency_code}}', $flight->currency_code, $html_template);
        $html_template = str_replace('{{base_fare}}', $flight->base_fare, $html_template);
        $html_template = str_replace('{{total_taxes}}', $flight->total_taxes, $html_template);
        $html_template = str_replace('{{extra}}', $flight->extra, $html_template);
        $html_template = str_replace('{{total}}', $flight->total, $html_template);

        // create segments html
        $segments_html = '';
        foreach ($flight_segments as $key => $segment) {
            $index = $key + 1;
            $segments_html .= '<tr>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;" colspan="4">' . $index . '. ' . $segment->airline_name . ' (' . $segment->airline_code . ') - vuelo #' . $segment->flight_number . '</th>';
            $segments_html .= '</tr>';
            $segments_html .= '<tr>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Desde: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->origin_airport_code . '</td>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Hora de salida: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->departure_date_time . '</td>';
            $segments_html .= '</tr>';
            $segments_html .= '<tr>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Hasta: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->destination_airport_code . '</td>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Hora de llegada: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->arrival_date_time . '</td>';
            $segments_html .= '</tr>';
            $segments_html .= '<tr>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Duracion: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->duration . '</td>';
            $segments_html .= '<th style="padding: 5px 0px 0px 10px; text-align: right;">Clase: </th>';
            $segments_html .= '<td style="padding: 5px 0px 0px 10px;">' . $segment->res_book_desig . '</td>';
            $segments_html .= '</tr>';
        }

        // replace segments in template
        $html_template = str_replace('{{segment_section}}', $segments_html, $html_template);

        // create flight_link 
        $flight_link = get_site_url() . '/wp-admin/admin.php?page=flight-management&booking_id=' . $flight->booking_id;
        
        // replce flight_link in template html
        $html_template = str_replace('{{flight_link}}', $flight_link, $html_template);

        // create puncana footer image
        $puncana_footer_image = FLIGHT_MANAGEMENT_URL . 'assets/images/puncana-footer.jpg';

        // replace image_footer in template html
        $html_template = str_replace('{{image_footer}}', $puncana_footer_image, $html_template);

        // send email to user
        wp_mail($user_email, 'Reserva de vuelo', $html_template, [
            'Content-Type: text/html; charset=UTF-8',
        ]);
    }

    public function testEmail () {
        $flight_test = new FlightManagementModel();
        $flight_test = $flight_test->getFlightById(1);

        $flight_contact_test = new FlightContactModel();
        $flight_contact_test = $flight_contact_test->get_contact_by_flight_id($flight_test->id);
        $this->sendEmailToAdmin($flight_test, $flight_contact_test);

        $flight_segments = new FlightSegmentModel();
        $flight_segments = $flight_segments->get_by_flight_id($flight_test->id);

        // send email to user
        $this->sendEmailToUser('anthonydeyvis32@gmail.com', $flight_test, $flight_contact_test, $flight_segments);


        return rest_ensure_response([
            'status' => 'success',
            'message' => 'Email enviado correctamente.'
        ]);
    }
}
