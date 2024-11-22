import { BASE_URL } from "./constants.js";
export const writeToSessionStorage = (key, value) => {
    if (typeof value === "object") {
        value = JSON.stringify(value);
    }

    sessionStorage.setItem(key, value);
};

export const readFromSessionStorage = (key) => {
    return JSON.parse(sessionStorage.getItem(key));
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

export const check401 = (res) => {
    if (res.status === 401) {
        sendBackTo();
    }
};

export const handleAdminLoggedIn = () => {
    fetch(`${BASE_URL}/admin/info`, {
        credentials: "include",
    })
        .then((res) => {
            if (res.status == 401) {
                logout();
                sendBackTo();
            }
        })
        .catch((e) => console.log({ e }));
    if (readFromSessionStorage("isAdminLoggedIn") !== true) {
        sendBackTo();
    }
};

export const htmlDateAndTimeTomysqlDatetime = (date, time) => {
    const [year, month, day] = date.split("-");
    const [hours, minutes] = time.split(":");
    return `${year}-${month}-${day} ${hours}:${minutes}:00`;
};

export const handleUserLoggedIn = (redirect = "index.html") => {
    fetch(`${BASE_URL}/user/info`, {
        credentials: "include",
    })
        .then((res) => {
            if (res.status == 401) {
                logout();
                sendBackTo("./login.html");
            }
        })
        .catch((e) => console.log({ e }));
    if (readFromSessionStorage("isUserLoggedIn") !== true) {
        sendBackTo(redirect);
    }
};

export const handleError = async (err) => {
    const e = await err.json();
    alert(e.error);
    console.log({ e });
    throw new Error(e.error);
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
    }

    const json = await req.json();
    console.log({ json });

    sessionStorage.removeItem("user");
    sessionStorage.removeItem("isLoggedIn");
    sessionStorage.removeItem("isAdminLoggedIn");
    sessionStorage.removeItem("isUserLoggedIn");
    sendBackTo(redirect);
};

export const handleLogout = async (e) => {
    e.preventDefault();
    try {
        const res = await logout();
    } catch (error) {
        console.log(error);
        alert("Failed to log out");
    }
};
