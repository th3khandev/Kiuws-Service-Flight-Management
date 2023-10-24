import { get, post } from "./api";

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

export const getFlightPrice = (flightSegments) => {
  return post("get-flight-price", {
    flight_segments: flightSegments,
  });
};
