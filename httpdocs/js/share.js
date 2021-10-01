// Create a share URL popover and copy the desired URL to the user's clipboard
// Modeled after stackoverflow's share answer popover
function shareTimelineEvent(id) {
    // Garbage collection, JUST IN CASE
    closeSharePopover(true);

    // Create a new div which will be our popup containing the form with the URL and a copy button
    let el = document.createElement("div"),
        value = "https://gmetimeline.com/#" + id;

    el.classList.add("popover__content");
    el.classList.add("active");
    el.addEventListener("focusout", closeSharePopover);
    el.innerHTML =
        '    <label class="share" for="urlToCopy">Share a link to this timeline event</label>\n' +
        '    <span class="share"><a onclick="closeSharePopover()" style="cursor: pointer;">X</a></span><br>\n' +
        '    <input class="share" type="text" id="urlToCopy" value="' + value + '" readonly><br>\n' +
        '    <button class="share" id="shareButton" onclick="copyShareUrl(true)">Copy link</button>\n' +
        '    <button class="share" id="closeShare" onclick="closeSharePopover()">Close</button>';

    // Prepend the element within the <article>
    document.getElementById(id).prepend(el);
}


// Select the url in the form and copy it to clipboard
function copyShareUrl(close) {
    let url = document.getElementById("urlToCopy");
    url.focus();
    url.select();
    document.execCommand("copy");
    showToast('Link copied to clipboard.');

    if (typeof(close) !== "undefined" && close) {
        closeSharePopover();
    }
}


// Close the share timeline event popover
function closeSharePopover() {
    let popover = document.querySelector('.popover__content.active');
    console.log({popover:popover});

    if (typeof(popover) !== "undefined" && popover !== null) {
        // The delay is needed because DIV is deleted before clipboard copies. This forces copy before deletion
        setTimeout(()=>{popover.remove()}, 1);
    }
}


function showToast(msg) {
    if (typeof(msg) === "undefined") {
        msg = "Ooopsie, forgot the msg!";
    }

    let snackbar = document.getElementById("snackbar");

    snackbar.className = "active";
    snackbar.innerText = msg;

    // After 3 seconds, remove the active class from DIV
    setTimeout(()=>{snackbar.classList.remove('active')}, 3000);
}



// Iterate through all tag filters to see which are active/inactive and build resulting hashtag
function buildUrlHash() {
    let tagFilters = document.querySelectorAll('header #tagFilters .tagFilter a'),
        args = [];

    for (let i = 0; i < tagFilters.length; i++) {
        // console.log({filter:tagFilters[i]});
        // Shorten the tag DIV ids to make the URL more manageable
        let id = tagFilters[i].id.substr(4);

        // If either Show ALL/NONE is active then no need to iterate further
        if (id === "ALL" && tagFilters[i].classList.contains('active')) {
            // This is the default view, don't bother setting a hash
            // This assumes the global filters will always be the first 2 items in the array
            // However, do an additional check to see if HEADLINES was toggled
            args = (document.getElementById("tag-headlines").classList.contains('inactive'))
                ? []
                : ['headlines=active'];

            break;
        } else if (id === "NONE" && tagFilters[i].classList.contains('active')) {
            // args = ['NONE=active'];
            args = (document.getElementById("tag-headlines").classList.contains('inactive'))
                ? ['NONE=active']
                : ['NONE=active','headlines=active'];

            break;
        }

        // Otherwise list out the entire filters' states
        args.push(id + '=' + tagFilters[i].classList[0]);
    }

    // If both ALL and NONE are set to inactive, remove them from the hash to prevent view restore bug
    // Also remove any of the =active tags
    //   because default load is everything but HIDE ALL is active, so we only need to know which tags to hide
    let all = args.indexOf('ALL=inactive'),
        none = args.indexOf('NONE=inactive');

    if (all !== -1 && none !== -1) {
        // args = args.filter((value)=>{ return !(value === 'ALL=inactive' || value === 'NONE=inactive' || value.endsWith('=active')) });
        args = args.filter((value)=>{ return !(value === 'ALL=inactive' || value === 'NONE=inactive') });
    }

    // Assign the URL hash to the browser
    // Now if a user copy/pastes the URL the view state will also be shared
    // Do this in a timer to ensure it is properly set
    // setTimeout(()=>{ window.location.hash = '#' + args.join(',') }, 1);
    window.location.hash = '#' + args.join(',');
}


function readUrl() {
    let hash = window.location.hash,
        active = [],
        inactive = [];

    // If there's no hashtag, do nothing
    if (hash === "" || hash === null || typeof(window.location.hash) === "undefined") return;

    // If there's no filter arguments to parse (no comma, no equal sign in the hash)
    if (hash.search(",") === -1 || hash.search('=') === -1) {
        // If there is just the # symbol, do nothing
        if (hash.length <= 1) return;

        // If we are either looking at a specific year or a specific timeline event, do nothing
        if (hash.startsWith('#y20') || hash.startsWith('#bottom')) return;

        // This will show only timeline events and hide their text & sources
        // This will trigger because the parent hash.search(",") == -1 means there are no additional tags in the hash to parse
        if (hash === '#headlines=active') {
            hideHeadlines('active');
            return;
        } else if (hash === '#NONE=active') {
            filterAllTags(false);
            return;
        }

        // Looking at a specific timeline event
        const el = document.getElementById(hash.substr(1));

        if (el === null) return;

        el.querySelector('main.content').classList.add('highlighted');
        el.scrollIntoView();

        // Also re-scroll in 1 second because positioning might be recalculated after lazy loading
        setTimeout(()=>{el.scrollIntoView();}, 1000);

        // console.log({hash:hash, el:document.getElementById(hash.substr(1)), qs:el.querySelector('.content')});

        return;
    }

    // Get a list of filter arguments, then iterate through them
    let args = hash.substr(1).split(',');

    // If there's only one argument then a global filter is specified
    if (args.length === 1) filterAllTags(args[0].split('=')[1]);

    // Otherwise we must iterate through each filter and restore its state
    for (let i = 0; i < args.length; i++) {
        let split = args[i].split('=');

        // Handle global filters
        if (split[0] === 'NONE') {
            filterAllTags(split[1]);
            continue;
        } else if (split[0] === 'headlines') {
            hideHeadlines(split[1]);
            continue;
        }

        // Otherwise add each individual filter to either the active or inactive array for later processing
        if (split[1] === 'active') {
            active.push(split[0]);
        } else {
            inactive.push(split[0]);
        }
    }

    // Hide all inactive timeline events FIRST, then ensure the active events are visible
    if (inactive.length) inactive.map(tag => filterTag(tag, undefined, false));
    if (active.length) active.map(tag => filterTag(tag, undefined, true));
}
readUrl();