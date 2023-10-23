import { get } from './api'

export const getAirportCodes = (search ='') => {
  return get(`get-airport-codes?search=${search}`)
}