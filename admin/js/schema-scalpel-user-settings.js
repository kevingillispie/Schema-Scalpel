function lockUnlock(el) {
    let paramInput = document.getElementById('search_param');
    if (el.classList.contains("unlock") == true) {
        el.innerHTML = `<svg onclick="lockUnlock(this.parentElement)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>`;
        paramInput.setAttribute('disabled', 'true');
    } else {
        el.innerHTML = `<svg onclick="lockUnlock(this.parentElement)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2z"/></svg>`;
        paramInput.removeAttribute('disabled');
    }
    el.classList.toggle('unlock');
}

var tableOfPages = document.querySelectorAll("#excluded_schema tbody tr");

function pageSelected(rowIndex) {
    tableOfPages[rowIndex].classList.toggle("text-secondary");
}

let tabCount = 1;
let lineBreak = "\n";
let syntax = {
    "array": ["[", "]"],
    "object": ["{", "}"]
};

! function printWebPageExample() {
    let container = document.getElementById('website_example');
    formatSchemaExamples(container);
}();

! function printWebPageExample() {
    let container = document.getElementById('webpage_example');
    formatSchemaExamples(container);
}();

! function printBreadcrumbExample() {
    let container = document.getElementById('breadcrumb_example');
    formatSchemaExamples(container);
}();

function formatSchemaExamples(container) {
    let schema = container.dataset.schema;
    let jsonified = JSON.parse(`{"schema":` + schema + "}");
    for (const [key, value] of Object.entries(jsonified)) {
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][0]}</code>${lineBreak}`);

        printJSONLoop(container, value, tabCount);

        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][1]},</code>${lineBreak}`);
    }
}

function printJSONLoop(container, jObject, tab) {
    for (const [key, value] of Object.entries(jObject)) {
        if (checkType(value) == 'string') {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${isNumber(key) ? "" : `"${key}": `}"${value}",</code>${lineBreak}`);
        } else if (checkType(value) == "array") {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}"${key}": ${syntax["array"][0]}</code>${lineBreak}`);
            tab++;
            printJSONLoop(container, value, tab);
            tab--;
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${syntax["array"][1]},</code>${lineBreak}`);
        } else if (checkType(value) == 'object') {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${isNumber(key) ? "" : `"${key}": `}${syntax["object"][0]}</code>${lineBreak}`);
            tab++;
            printJSONLoop(container, value, tab);
            tab--;
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${syntax["object"][1]},</code>${lineBreak}`);
        }
    }
}

function isNumber(n) {
    return Number.isInteger(parseInt(n));
}

function checkType(thing) {
    if (Array.isArray(thing)) {
        return "array";
    }
    return typeof (thing);
}

function insertTabs(count) {
    let t = "";
    for (i = 0; i < count; i++) {
        t += "&#9;";
    }
    return t;
}