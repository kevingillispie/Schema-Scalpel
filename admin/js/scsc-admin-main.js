const TYPES = ["homepage", "global", "pages", "posts", "examples"];
const TYPE_TABS = document.querySelectorAll(".nav-link:not(.example-nav)");
const TYPE_TAB_CONTENTS = document.querySelectorAll(".tab-pane");

/**
 * Formatting displayed schema
 */
const TAB_COUNT = 1;
const LINE_BREAK = "\n";
const SYNTAX = {
    "array": ["[", "]"],
    "object": ["{", "}"]
};

/**
 * GET/SET ACTIVE TAB
 */
(function () {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    let activeIndex = -1;

    // Determine which tab should be active from URL param
    const tabFromUrl = params.get("set_tab");
    if (tabFromUrl && TYPES.includes(tabFromUrl)) {
        activeIndex = TYPES.indexOf(tabFromUrl);
        if (activeIndex < 0) activeIndex = 0;
    }

    // Ensure we don't go out of bounds (in case fewer tabs exist)
    if (activeIndex >= TYPE_TABS.length) {
        activeIndex = 0;
    }

    // Remove active classes from all
    TYPE_TABS.forEach(tab => {
        tab.classList.remove("active");
    });
    TYPE_TAB_CONTENTS.forEach(content => {
        content.classList.remove("active", "show");
    });

    // Activate the correct tab and content
    if (TYPE_TABS.length > 0 && -1 < activeIndex) {
        TYPE_TABS[activeIndex].classList.add("active");
        TYPE_TAB_CONTENTS[activeIndex].classList.add("active", "show");
    }

    // Add click listeners correctly (with proper closure)
    TYPE_TABS.forEach((tab, index) => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const targetType = TYPES[index];
            if (targetType) {
                setActiveTab(targetType);
            }
        }, { capture: true });
    });
})();

function setActiveTab(tab) {
    document.getElementById("tab_spinner").classList.remove("d-none");
    window.location.href = `/wp-admin/admin.php?page=scsc&update_tab=${tab}`;
}

/**
 * RESET PAGE AND POST SELECTIONS
 */
document.querySelectorAll("select").forEach(s => {
    s.selectedIndex = 0;
});

// Disable create buttons on Pages & Posts tabs by default
['pages', 'posts'].forEach(type => {
    const btn = document.getElementById(type + '_create_schema_code_block');
    if (btn) {
        btn.disabled = true;
        btn.dataset.id = '';
    }
});
/////////////////////

const EXISTING_SCHEMA = (type) => {
    switch (type) {
        case "homepage":
            return document.getElementById("current_homepage_schema");
        case "global":
            return document.getElementById("current_global_schema");
        case "pages":
            return document.getElementById("current_pages_schema");
        case "posts":
            return document.getElementById("current_posts_schema");
        default:
            return;
    }
}

TYPES.forEach(type => {
    if (EXISTING_SCHEMA(type)) {
        let container = EXISTING_SCHEMA(type).children;
        for (let i = 0; i < container.length; i++) {
            let jsonified = jsonify(container[i].dataset.schema);
            displaySchemaLoop(container[i], jsonified, TAB_COUNT, SYNTAX, LINE_BREAK);
        }
    }
});

(function () {
    let exampleFieldsets = document.querySelectorAll('#example_fieldsets [id^="example_schema"]');
    exampleFieldsets.forEach(field => {
        let container = field.children[0];
        let jsonified = jsonify(container.dataset.schema);
        displaySchemaLoop(container, jsonified, 1, SYNTAX, LINE_BREAK);
    })
})();

function displaySchemaLoop(container, jsonified, TAB_COUNT, SYNTAX, LINE_BREAK) {
    if (Array.isArray(jsonified)) {
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${SYNTAX["array"][0]}</code>${LINE_BREAK}`);
        jsonified.forEach((item, index) => {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(TAB_COUNT)}${SYNTAX["object"][0]}</code>${LINE_BREAK}`);
            printJSONLoop(container, item, TAB_COUNT + 1);
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(TAB_COUNT)}${SYNTAX["object"][1]}${index < jsonified.length - 1 ? "," : ""}</code>${LINE_BREAK}`);
        });
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${SYNTAX["array"][1]}</code>${LINE_BREAK}`);
    } else if (typeof jsonified === 'object' && jsonified !== null) {
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${SYNTAX["object"][0]}</code>${LINE_BREAK}`);
        printJSONLoop(container, jsonified, TAB_COUNT + 1);
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${SYNTAX["object"][1]}</code>${LINE_BREAK}`);
    }
}
/////////////////////

/**
 * Schema formatting functions.
 */
function jsonify(j) {
    // First attempt: try parsing directly
    try {
        return JSON.parse(j);
    } catch (e) {
        // If parsing fails, attempt to preprocess common HTML entities
        let cleaned = j;

        const replacements = [
            { regex: /<script\b[^>]*>[\s\S]*?<\/script>/gi, replacement: '' },
            { regex: /<\/?script\b[^>]*>/gi, replacement: '' },
            { regex: /&lt;/g, replacement: '<' },
            { regex: /&gt;/g, replacement: '>' },
            { regex: /&quot;/g, replacement: '"' },
            { regex: /&apos;/g, replacement: "'" },
            { regex: /&amp;/g, replacement: '&' },
            { regex: /<iframe\b[^>]*>[\s\S]*?<\/iframe>/gi, replacement: '' },
            { regex: /<\/?iframe\b[^>]*>/gi, replacement: '' }
        ];

        for (const { regex, replacement } of replacements) {
            cleaned = cleaned.replace(regex, replacement);
            try {
                JSON.parse(cleaned);  // Check if it can be parsed after each replace
                return cleaned;  // Return immediately if successful
            } catch (e) {
                // Continue to the next replacement if parsing fails
            }
        }

        // Final attempt: return error message
        console.error("Schema parsing failed even after cleanup:", e);
        console.error("Problematic schema string:", j.substring(0, 500) + "...");

        return {
            error: "Invalid or malformed schema (parsing failed).",
            raw: j
        };
    }
}

function correctBracketCount(w) {
    let leftBracketCount = 0,
        rightBracketCount = 0;
    for (let i = 0; i < w.length; i++) {
        if (w[i] == "{") {
            leftBracketCount++;
        } else if (w[i] == "}") {
            rightBracketCount++;
        }
    }
    if (leftBracketCount < rightBracketCount) {
        w = w.substring(0, w.length - 1);
        correctBracketCount(w);
    } else if (leftBracketCount > rightBracketCount) {
        w += "}";
        correctBracketCount(w);
    }
    return w;
}

function removeIllegalCharacters(v) {
    let w, x;
    v = v.trim();

    if (v.match(/{/gm) == null || v.match(/}/gm) == null) {
        const hasOpening = v.includes("{");
        const hasClosing = v.includes("}");

        let msg = "MISSING BRACKETS DETECTED!\n\n";

        if (!hasOpening && !hasClosing) {
            msg += "Schema appears to contain NO { } brackets at all.\n";
        } else if (!hasOpening) {
            msg += "Found closing } but NO opening { brackets!\n";
        } else {
            msg += "Found opening { but NO closing } brackets!\n";
        }

        msg += "\nFirst 120 characters:\n" + v.substring(0, 120).replace(/\s+/g, ' ');
        alert(msg);
    }

    if (v.match(/{/gm).length < v.match(/}/gm).length && v.substring(v.length - 2, v.length) === "}}") {
        v = v.substring(0, v.length - 1);
    }

    do {
        w = v;

        if (v.startsWith("{{")) v = v.substring(1);

        v = v.replaceAll(/[\u0000-\u001F\u007F\n\r\t]+/g, '')
            .replaceAll(" {", "{")
            .replaceAll("{ ", "{")
            .replaceAll(",}", "}")
            .replaceAll("} ", "}")
            .replaceAll("[ ", "[")
            .replaceAll(" [", "[")
            .replaceAll(",]", "]")
            .replaceAll(" ]", "]")
            .replaceAll("] ", "]")
            .replaceAll("{}", "")
            .replaceAll("[]", "")
            .replaceAll("{[", "")
            .replaceAll("  ", " ")
            .replaceAll(" ,", ",")
            .replaceAll("; ", ";")
            .replaceAll("::", ":");

        x = v;
    } while (x !== w);

    if (v.endsWith(",")) {
        v = v.substring(0, v.length - 1);
    }

    let formatted = finalFormattingCheck(correctBracketCount(v));
    formatted = formatted.replaceAll(`"`, `&quot;`).replaceAll(`'`, `&apos;`);
    return formatted;
}


function finalFormattingCheck(theSchema) {
    try {
        JSON.parse(theSchema);
        return theSchema;
    } catch (e) {
        let preview = theSchema.trim().substring(0, 160);
        if (theSchema.length > 160) preview += "…";

        alert(
            "Final formatting check failed — schema is still invalid JSON\n\n" +
            "Error: " + (e.message || e) + "\n\n" +
            "Tip: most common mistakes at this stage are:\n" +
            "• trailing or missing comma\n" +
            "• missing closing } or ]\n" +
            "• control characters that survived cleaning"
        );

        return { success: false, error: e.message, schema: preview };
    }
}
/////////////////////////

function deleteCurrentSchema(id, event) {
    event.preventDefault();
    let request = new XMLHttpRequest();
    request.onreadystatechange = () => {
        if (request.readyState == 4) {
            location.reload();
        }
    }
    request.open("GET", "/wp-admin/admin.php?page=scsc&delete=" + id + `&nonce=${encodeURIComponent(scscNonces.delete)}`);
    request.send();
}

/**
 * FILTER PAGE/POST LIST
 */
document.querySelectorAll('[id*="_filter"]').forEach(input => {
    input.addEventListener('keyup', (e) => filterList(input.id.substring(0, 5), e.target.value))
});
function filterList(kind, value) {
    let list = document.querySelector('#' + kind + '_list').children;
    for (let i = 1; i < list.length; i++) {
        if (list[i].innerText.toLowerCase().search(value.toLowerCase()) >= 0) {
            list[i].classList.remove('d-none');
        } else {
            list[i].classList.add('d-none');
        }
    }
}

/**
 * SELECT POST => UPDATE CREATE BUTTON
 */
document.querySelectorAll('[id*="_list"] li').forEach(list => {
    list.addEventListener("click", function () {
        onPostSelectChange(this);
    });
});

function disableCreateSchemaButtons(button) {
    button.setAttribute("disabled", "true");
}

function updateBlogPostingForm(postID) {
    if (postID) {
        document.getElementById("selectedPost").innerText = "(ID# " + postID + ")";
        document.getElementById("updateType2").setAttribute("value", postID);
        document.getElementById("updateType2").removeAttribute("disabled");
    } else {
        document.getElementById("selectedPost").innerText = "";
        document.getElementById("updateType2").setAttribute("disabled", "true");
    }
}

function onPostSelectChange(el) {
    let type = el.id.substring(0, 5) || el.parentElement.id.substring(0, 5);
    let postID = el.value || el.dataset.value;
    if (el.dataset.type === 'post') {
        updateBlogPostingForm(postID);
    }
    document.querySelectorAll(".edit-block-button").forEach(btn => {
        btn.dataset.id = postID;
    });

    const createCodeBlockBtn = document.getElementById(type + '_create_schema_code_block');
    const createSchemaBtn = document.querySelector('button[id^="' + type + '_schema_create"]');
    const buttons = [createCodeBlockBtn, createSchemaBtn].filter(Boolean);

    buttons.forEach(btn => {
        if (btn) {
            btn.dataset.id = postID || '';
            if (postID && postID !== '' && postID !== '-1') {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }
    });

    // This is critical — keep the schema display call!
    if (postID && postID !== '' && postID !== '-1') {
        showPostSchema(postID, type);
    }

    let activePageOrPost = (type == "pages") ? "active_page" : "active_post";
    let request = new XMLHttpRequest();
    request.open("GET", "/wp-admin/admin.php?page=scsc&" + activePageOrPost + "=" + postID);
    request.send();
}

function showPostSchema(id, type) {
    // Update legend
    const selectedItem = document.querySelector(`li[data-value="${id}"]`);
    if (selectedItem) {
        document.querySelector(`#${type}_schema legend`).textContent =
            'Current: ' + selectedItem.dataset.title;
    }

    // Hide all, show only matching
    document.querySelectorAll('#current_pages_schema pre, #current_posts_schema pre').forEach(pre => {
        if (pre.classList.contains('post-id-' + id)) {
            pre.classList.remove('d-none');
        } else {
            pre.classList.add('d-none');
        }
    });
}
////////////////////

/**
 * Print schema
 */
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

function printJSONLoop(container, jObject, tab) {
    for (const [key, value] of Object.entries(jObject)) {
        if (checkType(value) == 'string') {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${isNumber(key) ? "" : `"${key}": `}"${value}",</code>${LINE_BREAK}`);
        } else if (checkType(value) == "array") {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}"${key}": ${SYNTAX["array"][0]}</code>${LINE_BREAK}`);
            tab++;
            printJSONLoop(container, value, tab);
            tab--;
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${SYNTAX["array"][1]},</code>${LINE_BREAK}`);
        } else if (checkType(value) == 'object') {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${isNumber(key) ? "" : `"${key}": `}${SYNTAX["object"][0]}</code>${LINE_BREAK}`);
            tab++;
            printJSONLoop(container, value, tab);
            tab--;
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tab)}${SYNTAX["object"][1]},</code>${LINE_BREAK}`);
        }
    }
}
//////////////////////////

function createNewSchema(type, id) {
    console.log(type, id);
    let schema = "";
    schema = document.querySelector("#schemaTextareaCreate").value;
    document.querySelector("#schemaBlockSave").dataset.id = id;
    let formatted = removeIllegalCharacters(schema);
    let request = new XMLHttpRequest();
    request.onreadystatechange = () => {
        if (request.readyState == 4) location.reload();
    }
    request.open("POST", "/wp-admin/admin.php?page=scsc");
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    let data = `schemaType=${encodeURIComponent(type)}&postID=${encodeURIComponent(id)}&create=${encodeURIComponent(formatted)}&nonce=${encodeURIComponent(scscNonces.create)}`;
    request.send(data);
}

function updateCurrentSchema(id, event) {
    event.preventDefault();
    let schema = document.querySelector("#schemaTextareaEdit").value;
    let formatted = removeIllegalCharacters(schema);
    let request = new XMLHttpRequest();

    request.onreadystatechange = () => {
        if (request.readyState == 4) {
            location.reload();
        }
    }

    request.open("POST", "/wp-admin/admin.php?page=scsc");
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    let data = `update=${encodeURIComponent(id)}&schema=${encodeURIComponent(formatted)}&nonce=${encodeURIComponent(scscNonces.update)}`;
    request.send(data);
}

function getSchemaIDFromDataAttribute(element) {
    let id = element.dataset.id;
    if (isNaN(parseInt(id))) {
        id = getSchemaIDFromDataAttribute(element.parentElement);
    }
    return id;
}

function editSchemaCodeBlock(id, event) {
    event.preventDefault();
    var schema = "";
    if (isNaN(parseInt(id))) {
        id = getSchemaIDFromDataAttribute(event.target);
    }
    schema = document.querySelector('pre[data-id="' + id + '"]').innerText;
    document.querySelector("#schemaBlockEditSaveButton").dataset.id = id;
    document.querySelector("#schemaBlockEditDeleteButton").dataset.id = id;
    document.querySelector("#schemaTextareaEdit").innerHTML = schema;
    document.querySelector("#schemaTextareaEdit").value = schema;
    document.getElementsByTagName("BODY")[0].style.overflow = "hidden";
    let modal = document.querySelector("#schemaBlockEditModal");
    modal.classList.add("show");
    modal.removeAttribute("aria-hidden");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("role", "dialog");
    modal.setAttribute("style", "display:block;background-color:rgba(0,0,0,.5);");
}

(function () {
    var params = new URLSearchParams(document.location.search);
    var currentTabName = params.get("set_tab");
    document.querySelectorAll('.edit-block').forEach(element => {
        element.addEventListener('click', (e) => {
            editSchemaCodeBlock(e.target.dataset.id, e);
        });
    });
    document.getElementById('schemaBlockEditSaveButton').addEventListener('click', (e) => {
        updateCurrentSchema(e.target.dataset.id, event);
    });
    document.getElementById('schemaBlockSave').addEventListener('click', (e) => {
        createNewSchema(currentTabName, document.querySelector('#' + currentTabName + '_create_schema_code_block').dataset.id);
    });
})();

function closeSchemaTextareaEditModal(event) {
    event.preventDefault();
    document.querySelector("#schemaBlockEditSaveButton").dataset.id = "";
    document.querySelector("#schemaBlockEditDeleteButton").dataset.id = "";
    document.querySelector("#schemaTextareaEdit").innerHTML = "";
    document.querySelector("#schemaTextareaEdit").value = "";
    document.getElementsByTagName("BODY")[0].style.overflow = "";
    let modal = document.querySelector("#schemaBlockEditModal");
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
    modal.removeAttribute("aria-modal");
    modal.removeAttribute("role");
    modal.removeAttribute("style");
}
