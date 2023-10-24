import { get } from "./api";

export const getFlightsAvailable = (
  origin,
  destination,
  depurateDate,
  returnDate,
  adults,
  children
) => {
  return get(
    `get-flight-available?origin=${origin}&destination=${destination}&depurate_date=${depurateDate}&return_date=${returnDate}&adults=${adults}&children=${children}`
  );
};

export const getFlightPrice = (
  departureDateTime,
  arrivalDateTime,
  origin,
  destination,
  adults,
  children,
  flightNumber,
  resBookDesig,
  airlineCode
) => {
  return get(
    `get-flight-price?departureDateTime=${departureDateTime}&arrivalDateTime=${arrivalDateTime}&origin=${origin}&destination=${destination}&adults=${adults}&children=${children}&flightNumber=${flightNumber}&resBookDesig=${resBookDesig}&airlineCode=${airlineCode}`
  );
};
