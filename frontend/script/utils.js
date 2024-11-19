import { BASE_URL } from "./constants.js";
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

export const addElementToElementOnCondition = (element, condition, elementToAdd) => {
    if (condition) {
        element.appendChild(elementToAdd);
    }
};

export const sendBackTo = (location = "index.html") => {
    window.location.href = location;
};

export const logout = async (redirect = "index.html") => {
    const req = await fetch(`${BASE_URL}/admin/logout`, {
        method: "POST",
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        const err = await req.json();
        console.log({ err });
        throw new Error("Failed to log out");
        return;
    }

    const json = await req.json();
    console.log({ json });

    sessionStorage.removeItem("isLoggedIn");
    sendBackTo(redirect);
};
