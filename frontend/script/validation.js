/**
 * @module validation
 */

const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z.]{2,6}$/;
const passwordRegexes = [
    {
        msg: "Must be at least 8 characters long",
        regex: /.{8,}/,
    },
    {
        msg: "Must have at least one uppercase letter",
        regex: /[A-Z]/,
    },
    {
        msg: "Must include at least 3 digits",
        regex: /\d{3,}/,
    },
    {
        msg: "Must contain at least one special character",
        regex: /[!@#$%^&*()\-_=+\|{};:/?.>]/,
    },
];

/**
 * @param {string} email The email to validate
 * @returns {boolean} true if email is valid, false otherwise
 */
export const validateEmail = (email) => {
    return emailRegex.test(email);
};

/**
 * @param {string} password The password to validate
 * @returns {{result: boolean, problems: string[]} } An object with the result of the validation and an array of problems
 */
export const validatePassword = (password) => {
    let problems = [];
    let result = true;

    const boolArr = passwordRegexes.map((item) => {
        const { msg, regex } = item;
        const matchRes = regex.test(password);
        if (!matchRes) problems.push(msg);
        if (result) {
            result = matchRes;
        }
    });

    return {
        result: result,
        problems: problems,
    };
};

/**
 * @param {HTMLInputElement} inputField The input field to validate
 * @returns {boolean} true if the input field is filled, false otherwise
 */
export const validateFilled = (input) => {
    if (typeof input === "string") {
        return input.length > 0;
    }

    return input.value !== "";
};

/**
 * @param {HTMLInputElement[]} fields An array of inputs to check
 */
export const validateFieldsFilled = (fields) => {
    if (!fields.every((f) => validateFilled(f))) {
        alert("All fields must be filled!");
        return false;
    }
    return true;
};

/**
 * @param {string} email Email
 */
export const handleEmail = (email) => {
    if (!validateEmail(email)) {
        alert("Invalid email!");
        return false;
    }
    return true;
};

export const handleSalary = (salary, thresh = 100) => {
    try {
        var sal = parseFloat(salary);
    } catch (e) {
        alert("Invalid salary!");
        return false;
    }

    if (isNaN(sal) || sal < 0) {
        alert("Invalid salary!");
        return false;
    }

    if (sal < thresh) {
        alert("Salary must be at least " + thresh);
        return false;
    }
    return true;
};

/**
 * @param {string} password
 */
export const handlePasswordProblems = (password) => {
    const { result, problems } = validatePassword(password);
    if (!result) {
        alert(problems.join("\n"));
        return false;
    }
    return true;
};
