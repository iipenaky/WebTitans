export const writeToSessionStorage = (key, value) => {
  if (typeof value === "object") {
    value = JSON.stringify(value);
  }

  sessionStorage.setItem(key, value);
};

export const readFromSessionStorage = (key) => {
  return sessionStorage.getItem(key);
};

export const writeToCookieStorage = (key, value) => {
  if (typeof value === "object") {
    value = JSON.stringify(value);
  }

  document.cookie = `${key}=${value}`;
};

export const readFromCookieStorage = (key) => {
  console.log(document.cookie);
  return document.cookie.split(";").find((cookie) => {
    return cookie.includes(key);
  });
};
