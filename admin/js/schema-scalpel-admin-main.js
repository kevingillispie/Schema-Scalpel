const TYPES = ["homepage", "global", "pages", "posts", "examples"];
const CURRENT_SCHEMA_BY_POST_ID = [document.getElementById('current_pages_schema').children, document.getElementById('current_posts_schema').children];
const TYPE_TABS = document.querySelectorAll(".nav-link:not(.example-nav)");
const TYPE_TAB_CONTENTS = document.querySelectorAll(".tab-pane");

/**
 * GET/SET ACTIVE TAB
 */
! function () {
    var url = new URL(window.location.href);
    var params = new URLSearchParams(url.search);

    TYPE_TABS.forEach(tab => {
        tab.classList.remove("active");
        tab.classList.remove("show");
    });
    let i = TYPES.indexOf(params.get('set_tab'));
    TYPE_TABS[i].classList.add("active");
    TYPE_TAB_CONTENTS[i].classList.add("active");
    TYPE_TAB_CONTENTS[i].classList.add("show");
    TYPE_TABS.forEach((tab, i) => {
        tab.addEventListener('click', function () {
            setActiveTab(TYPES[i]);
        });
    });
}();

function setActiveTab(tab) {
    document.getElementById("tab_spinner").classList.remove("d-none");
    window.location.href = `/wp-admin/admin.php?page=scsc&update_tab=${tab}`;
}

/**
 * 
 */

/**
 * RESET PAGE AND POST SELECTIONS
 */
document.querySelectorAll("select").forEach(s => {
    s.selectedIndex = 0;
});

TYPES.forEach(type => {
    if (type == "pages" && type == "posts") {
        document.querySelector("button[id^=\"" + type + "_schema_create\"").setAttribute("disabled", "true");
    }
});
/**
 * 
 */

document.querySelectorAll("[id*='add_new_line_after']").forEach(el => {
    el.value = "";
});

var schemaPreview = (type) => {
    switch (type) {
        case "homepage":
            return document.getElementById("homepage_schema_preview");
        case "global":
            return document.getElementById("global_schema_preview");
        case "pages":
            return document.getElementById("pages_schema_preview");
        case "posts":
            return document.getElementById("posts_schema_preview");
        default:
            return;
    }
}

var existingSchema = (type) => {
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

var tabCount = 1;
var lineBreak = "\n";
var syntax = {
    "array": ["[", "]"],
    "object": ["{", "}"]
}

TYPES.forEach(type => {
    if (existingSchema(type)) {
        let container = existingSchema(type).children;
        for (let i = 0; i < container.length; i++) {
            let jsonified = jsonify(container[i].dataset.schema);

            displaySchemaLoop(container[i], jsonified, tabCount, syntax, lineBreak);
        }
    }
});

! function () {
    let exampleFieldsets = document.querySelectorAll('#example_fieldsets [id^="example_schema"]');
    exampleFieldsets.forEach(field => {
        let container = field.children[0];
        let jsonified = jsonify(container.dataset.schema);
        displaySchemaLoop(container, jsonified, 1, syntax, lineBreak);
    })
}();

function displaySchemaLoop(container, jsonified, tabCount, syntax, lineBreak) {
    for (const [key, value] of Object.entries(jsonified)) {
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][0]}</code>${lineBreak}`);
        printJSONLoop(container, value, tabCount);
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][1]},</code>${lineBreak}`);
    }
}

function jsonify(j) {
    return JSON.parse('{"schema":' + j + '}');
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
    if (v.match(/{/gm) == null || v.match(/}/gm) == null) alert("ILLEGAL CHARACTER: An error in SYNTAX has been detected.\nPlease review your schema.");
    (v.match(/{/gm).length < v.match(/}/gm).length && v.substring(v.length - 2, v.length) == "}}") ? v = v.substring(0, v.length - 1) : "";
    do {
        w = v;
        (v.substring(0, 2) == "{{") ? v = v.substring(1, v.length) : "";
        v = v.replaceAll(/(\n|\r|\t)/gmi, "")
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
            .replaceAll(",  ", ", ")
            .replaceAll(" ,", ",")
            .replaceAll(": ", ":")
            .replaceAll("; ", ";")
            .replaceAll("::", ":");
        x = v;
    } while (x !== w);
    if (w[w.length - 1] == ",") {
        w = w.substring(0, w.length - 1);
    }
    let formatted = finalFormattingCheck(correctBracketCount(w));
    formatted = formatted.replaceAll("\"", "&quot;").replaceAll("\'", "&apos;");
    return formatted;
}

function removePostTitle(s) {
    return s.substring(s.search("{"));
}

function finalFormattingCheck(theSchema) {
    try {
        JSON.parse(theSchema);
        return theSchema;
    } catch(e) {
        alert("There is an error in your schema. Please review.\n\n" + e);
    }
}

function createNewSchema(type, id, from = "") {
    let schema = "";
    if (from == "block") {
        schema = document.querySelector("#schemaTextareaEdit").value;
    } else {
        for (let i = 0; i < schemaPreview(type).children.length; i++) {
            schema += schemaPreview(type).children[i].innerText;
        }
    }

    let formatted = removeIllegalCharacters(schema);
    let request = new XMLHttpRequest();
    request.onreadystatechange = () => {
        if (request.readyState == 4) location.reload();
    }
    request.open("GET", '/wp-admin/admin.php?page=scsc&schemaType=' + type + '&postID=' + id + '&create=' + encodeURIComponent(formatted));
    request.send();
}

function updateCurrentSchema(id, from = "", event) {
    event.preventDefault();
    let schema = (from == "line") ? document.querySelector("pre[data-id=\"" + id + "\"]").innerText : document.querySelector("#schemaTextareaEdit").value;
    schema = removePostTitle(schema);
    let formatted = removeIllegalCharacters(schema);
    let request = new XMLHttpRequest();

    request.onreadystatechange = () => {
        if (request.readyState == 4) {
            location.reload();
        }
    }
    request.open("GET", "/wp-admin/admin.php?page=scsc&update=" + id + "&schema=" + encodeURIComponent(formatted));
    request.send();
}

function deleteCurrentSchema(id, event) {
    event.preventDefault();
    let request = new XMLHttpRequest();
    request.onreadystatechange = () => {
        if (request.readyState == 4) {
            location.reload();
        }
    }
    request.open("GET", "/wp-admin/admin.php?page=scsc&delete=" + id);
    request.send();
}

/**
 * SELECT POST => UPDATE CREATE BUTTON
 */

document.querySelectorAll("[id*=\"_list\"]").forEach(list => {
    list.addEventListener("change", function () {
        onPostSelectChange(this);
    });
});

function onPostSelectChange(el) {
    let type = el.id.substring(0, 5);
    document.querySelectorAll(".edit-block-button").forEach(btn => {
        btn.dataset.id = el.value;
    });
    let buttons = [document.querySelector("button[id^=\"" + type + "_edit_schema_code_block\""), document.querySelector("button[id^=\"" + type + "_schema_create\"")];
    console.log(buttons);
    buttons.forEach(btn => {
        btn.dataset.id = el.value;
        if (el.selectedIndex != 0) {
            btn.removeAttribute("disabled");
            showPostSchema(el.value);
        } else {
            disableCreateSchemaButtons(btn);
        }
    });
    let activePageOrPost = (type == "pages") ? "active_page" : "active_post";
    let request = new XMLHttpRequest();
    request.open("GET", "/wp-admin/admin.php?page=scsc&" + activePageOrPost + "=" + el.value);
    request.send();
}

function showPostSchema(id) {
    CURRENT_SCHEMA_BY_POST_ID.forEach(post_type => {
        for (i in post_type) {
            if (!post_type[i].dataset) continue;
            if (post_type[i].dataset.postId == id) {
                post_type[i].classList.remove("d-none");
            } else {
                (!post_type[i].classList.contains("d-none")) ? post_type[i].classList.add("d-none") : "";
            }
        }
    });
}

function disableCreateSchemaButtons(button) {
    button.setAttribute("disabled", "true");
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

function editSchemaCodeBlock(type, isNew, id, event) {
    event.preventDefault();
    var schema = "";
    if (isNew == "new") {
        document.querySelector("#schemaBlockEditSave").setAttribute("onclick", `createNewSchema('${type}', '${id}', 'block', event)`);
        schema = document.querySelector("pre[data-create=\"new\"]").innerText;
    } else {
        schema = document.querySelector("pre[data-id=\"" + id + "\"]").innerText;
    }
    document.querySelector("#schemaBlockEditSave").dataset.id = id;
    document.querySelector("#schemaBlockEditDelete").dataset.id = id;
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

function closeSchemaTextareaEditModal(event) {
    event.preventDefault();
    document.querySelector("#schemaBlockEditSave").dataset.id = "";
    document.querySelector("#schemaBlockEditDelete").dataset.id = "";
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
