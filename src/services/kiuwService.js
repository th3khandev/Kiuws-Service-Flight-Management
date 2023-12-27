import { get, post } from "./api";

export const getFlightsAvailable = (
  origin,
  destination,
  depurateDate,
  returnDate,
  adults,
  children,
  inf,
  tripType,
  onlyDirectFlights
) => {
  return get(
    `get-flight-available?origin=${origin}&destination=${destination}&depurate_date=${depurateDate}&return_date=${returnDate}&adults=${adults}&children=${children}&inf=${inf}&trip_type=${tripType}&only_direct_flights=${onlyDirectFlights ? 1 : 0}`
  );
};

export const getFlightPrice = (departureFlightSegments, returnFlightSegments) => {
  return post("get-flight-price", {
    departure_flight_segments: departureFlightSegments,
    return_flight_segments: returnFlightSegments
  });
};

export const createReservation = (reservationData) => {
  return post("create-reservation", reservationData);
}


export const processPayment = (cardName, cardDocumentNumber, cardNumber, cardEmail, amount, currencyCode, flightBookingId, cardToken) => {
  return post("process-payment", {
    card_name: cardName,
    card_document_number: cardDocumentNumber,
    card_number: cardNumber,
    card_email: cardEmail,
    amount: amount,
    currency_code: currencyCode,
    flight_booking_id: flightBookingId,
    card_token: cardToken
  });
}