const TYPES = ["homepage", "global", "pages", "posts", "examples"];
const TYPE_TABS = document.querySelectorAll(".nav-link:not(.example-nav)");
const TYPE_TAB_CONTENTS = document.querySelectorAll(".tab-pane");

/**
 * GET/SET ACTIVE TAB
 */
(function () {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    let activeIndex = 0; // Default to first tab

    // Determine which tab should be active from URL param
    const tabFromUrl = params.get("set_tab");
    if (tabFromUrl && TYPES.includes(tabFromUrl)) {
        activeIndex = TYPES.indexOf(tabFromUrl);
        if (activeIndex === -1) activeIndex = 0;
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
    if (TYPE_TABS.length > 0) {
        TYPE_TABS[activeIndex].classList.add("active");
        TYPE_TAB_CONTENTS[activeIndex].classList.add("active", "show");
    }

    // Add click listeners correctly (with proper closure)
    TYPE_TABS.forEach((tab, index) => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const targetType = TYPES[index];
            if (targetType) {
                setActiveTab(targetType);
            }
        });
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

TYPES.forEach(type => {
    if (type == "pages" && type == "posts") {
        document.querySelector('button[id^="' + type + '_schema_create"').setAttribute("disabled", "true");
    }
});
/////////////////////

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

/**
 * Formatting displayed schema
 */
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
    // If jsonified is an array, wrap it in square brackets
    if (Array.isArray(jsonified)) {
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["array"][0]}</code>${lineBreak}`);
        jsonified.forEach((item, index) => {
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tabCount)}${syntax["object"][0]}</code>${lineBreak}`);
            printJSONLoop(container, item, tabCount + 1);
            container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${insertTabs(tabCount)}${syntax["object"][1]}${index < jsonified.length - 1 ? "," : ""}</code>${lineBreak}`);
        });
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["array"][1]}</code>${lineBreak}`);
    } else {
        // Handle non-array (object) case, if needed
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][0]}</code>${lineBreak}`);
        printJSONLoop(container, jsonified, tabCount);
        container.insertAdjacentHTML("beforeend", `<code class="d-inline-block w-100">${syntax["object"][1]}</code>${lineBreak}`);
    }
}
/////////////////////

/**
 * Schema formatting functions.
 */
function jsonify(j) {
    return JSON.parse(j);
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
    v = v.trim()
    if (v.match(/{/gm) == null || v.match(/}/gm) == null) {
        alert("MISSING CHARACTER: An error in formatting has been detected.\nPlease review your schema.");
    }
    if (v.match(/{/gm).length < v.match(/}/gm).length && v.substring(v.length - 2, v.length) == "}}") {
        v = v.substring(0, v.length - 1);
    }
    do {
        w = v;

        if (v.substring(0, 2) == "{{") v = v.substring(1, v.length);

        v = v.replaceAll(/(\n|\r|\r\n|\n\r|\t)/g, "")
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

    if (w[w.length - 1] == ",") w = w.substring(0, w.length - 1);

    let formatted = finalFormattingCheck(correctBracketCount(w));
    formatted = formatted.replaceAll(`"`, `&quot;`).replaceAll(`'`, `&apos;`);
    return formatted;
}

function finalFormattingCheck(theSchema) {
    try {
        JSON.parse(theSchema);
        return theSchema;
    } catch (e) {
        alert("There is an error in your schema. Please review.\n\n" + e);
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
    request.open("GET", "/wp-admin/admin.php?page=scsc&delete=" + id);
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
    let indexInList = el.selectedIndex || el.dataset.index;
    if (el.dataset.type === 'post') {
        updateBlogPostingForm(postID);
    }
    document.querySelectorAll(".edit-block-button").forEach(btn => {
        btn.dataset.id = postID;
    });
    let buttons = [
        document.querySelector('button[id^="' + type + '_create_schema_code_block"'),
        document.querySelector('button[id^="' + type + '_schema_create"')
    ];
    buttons.forEach(btn => {
        if (btn) {
            btn.dataset.id = postID;
            if (0 <= parseInt(indexInList)) {
                btn.removeAttribute("disabled");
                showPostSchema(postID, type);
            } else {
                disableCreateSchemaButtons(btn);
            }
        }
    });
    let activePageOrPost = (type == "pages") ? "active_page" : "active_post";
    let request = new XMLHttpRequest();
    request.open("GET", "/wp-admin/admin.php?page=scsc&" + activePageOrPost + "=" + postID);
    request.send();
}

function showPostSchema(id, type) {
    document.querySelector('#' + type + '_schema legend').innerText = 'Current: ' + document.querySelector('li[data-value="' + id + '"]').dataset.title;
    var CURRENT_SCHEMA_BY_POST_ID = [document.getElementById('current_pages_schema').children, document.getElementById('current_posts_schema').children];
    CURRENT_SCHEMA_BY_POST_ID.forEach(post_type => {
        for (i in post_type) {
            if (isNaN(parseInt(i))) continue;
            if (post_type.length > 0 && post_type[i].classList.contains('post-id-' + id)) {
                post_type[i].classList.remove("d-none");
            } else {
                (!post_type[i].classList.contains("d-none")) ? post_type[i].classList.add("d-none") : "";
            }
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
    let data = `schemaType=${encodeURIComponent(type)}&postID=${encodeURIComponent(id)}&create=${encodeURIComponent(formatted)}`;
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
    let data = `update=${encodeURIComponent(id)}&schema=${encodeURIComponent(formatted)}`;
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

!function () {
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
}();

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
