const BASE_URL_PLUGIN_API = "/wp-json/kiuws-flight-management/v1";

export const get = (path) => {
  return fetch(`${BASE_URL_PLUGIN_API}/${path}`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });
};
