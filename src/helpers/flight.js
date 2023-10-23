/**
 * Get duration text
 * @param {string} duration 'HH:mm:ss'
 * @return {string}
 */
export const getDurationText = (duration) => {
  const text = duration.split(":");
  const hours = text[0];
  const minutes = text[1];
  return `${hours}h ${minutes}m`;
};

/**
 * Get time text
 * @param {string} datetime 'YYYY-MM-DD HH:mm:ss'
 * @return {string}
 * @example
 * getDateTimeText('2020-01-01 12:00:00') // 12:00 PM
 * getDateTimeText('2020-01-01 00:00:00') // 12:00 AM
 * getDateTimeText('2020-01-01 23:59:59') // 11:59 PM
 */
export const getDateTimeText = (datetime) => {
  const date = new Date(datetime);
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const ampm = hours >= 12 ? "PM" : "AM";
  const hours12 = hours % 12 || 12;

  const hoursText = hours12 < 10 ? `0${hours12}` : hours12;
  const minutesText = minutes < 10 ? `0${minutes}` : minutes;

  return `${hoursText}:${minutesText} ${ampm}`;
}
