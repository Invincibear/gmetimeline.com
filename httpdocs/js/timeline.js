// Jump to a specific timeline year
function jumpYear(year) {
    console.debug("jumpYear(year) called", {year:year});

    // Scroll to the bottom of the page
    if (year === 'moon') return scrollToBottom();

    // Otherwise scroll to the selected year
    document.getElementById('y' + year).scrollIntoView(true);
}


// Hides/un-hides all timeline cards of a specific year
function toggleYear(year) {
    let articleEls = document.getElementsByClassName(year),
        newYearLink = document.getElementById('y' + year).parentElement,
        hidden = newYearLink.classList.contains('hideAll');

    // Add/remove the hidden class to each individual article, based on the yearstamp's hideAll class being present
    for (let i = 0; i < articleEls.length; i++) {
        if (hidden) {
            articleEls[i].classList.remove('hidden');
        } else {
            articleEls[i].classList.add('hidden');
        }
    }

    // Finally, add/remove hideAll class to the yearstamp
    if (hidden) {
        document.getElementById('y' + year).parentElement.classList.remove('hideAll');
    } else {
        document.getElementById('y' + year).parentElement.classList.add('hideAll');
    }
}


/* This dynamically aligns the tag filters to the center of the page
** https://www.javascripttutorial.net/javascript-dom/javascript-width-height/
*/
function centerFilters() {
    let
        tagsX= document.getElementById('tagFilters').offsetWidth, // Computed width of #filterTags
        tag  = document.getElementsByClassName('tagFilter')[2], // Third tag filter DIV, which should be the first non-global tag filter
        tagX = tag.offsetWidth + 2, // Width of third tag filter DIV, including left + right margins (2px)
        modX = tagsX % tagX, // Remainder of pixels after dividing #filterTags by third tag filter DIV width
        padX = (modX / 2) + tag.style.marginLeft; // The remainder of the width of #filterTags divided by width of third tag filter DIV width, subtract margin widths

    /*    console.log({
            tagsX:tagsX,
            tag:tag,
            tagOffsetWidth:tag.offsetWidth,
            tagMarginLeft:tag.style.marginLeft,
            tagX:tagX,
            modX:modX,
            padX:padX
        });*/

    // Set the left padding to force centering of the collection of tag filters
    if (padX >= 1) {
        document.getElementById('tagFilters').style.paddingLeft = padX + 'px';
    }
}

// Run centerFilters() on load, and recall centerFilters() every time the window size changes (like phone rotation)
centerFilters();
window.addEventListener('resize', centerFilters);
document.addEventListener('resize', centerFilters);
window.addEventListener('orientationchange', centerFilters);


/* hides/un-hides timeline cards by tag */
function filterTag(tag, show, override) {
    const articles = document.querySelectorAll('.timeline article');
    const tagFilter = document.getElementById('tag-' + tag);
    console.log({tag:tag, show:show, override:override, articles:articles});

    if (tagFilter === null) return;

    for (let i = 0; i < articles.length; i++) {
        if (articles[i].dataset.tags.indexOf(tag) !== -1) {
            // Found the tag in this card, hide/un-hide it from the timeline

            // Overrides for SELECT ALL/NONE
            if (show !== undefined && show === true || override !== undefined && override === true) {
                // Show ALL tagged timeline cards
                articles[i].classList.remove('hidden');

                continue;
            } else if (show !== undefined &&  show === false || override !== undefined && override === false) {
                // Hide ALL tagged timeline cards
                articles[i].classList.add('hidden');

                continue;
            }

            // Toggle the visibility of the timeline card
            // Use the filter's current status to determine if we should hide/un-hide the card.
            // This prevents bug where flipping the card's "hidden" class breaks with multiple tags and differing visibility states
            if (!tagFilter.classList.contains('inactive')) {
                articles[i].classList.add('hidden');
            } else {
                articles[i].classList.remove('hidden');
            }
        }
    }


    if (typeof(override) !== "undefined") {
        // Change the color of the filter to indicate whether or not it is active/inactive
        tagFilter.className = (!override) ? 'inactive' : 'active';
    } else {
        // We are restoring a view state from URL hash
        tagFilter.className = (tagFilter.className === 'active') ? 'inactive' : 'active';
    }

    if (tagFilter.className === 'active') {
        tagFilter.parentElement.classList.remove('inactive');
        tagFilter.parentElement.classList.add('active');
    } else {
        tagFilter.parentElement.classList.remove('active');
        tagFilter.parentElement.classList.add('inactive');
    }

    // Turn the SELECT ALL & NONE filters off
    document.getElementById('tag-ALL').classList.remove('active');
    document.getElementById('tag-ALL').classList.add('inactive');
    document.getElementById('tag-NONE').classList.remove('active');
    document.getElementById('tag-NONE').classList.add('inactive');
    document.getElementById('tag-ALL').parentElement.classList.remove('active');
    document.getElementById('tag-ALL').parentElement.classList.add('inactive');
    document.getElementById('tag-NONE').parentElement.classList.remove('active');
    document.getElementById('tag-NONE').parentElement.classList.add('inactive');

    // Now handle showing the appropriate timeline dates next to the cards
    showFirstCardDatestamp();

    // And align the card to the appropriate side of the timeline
    alignCardToTimeline();

    // If filterTag() isn't being called from filterAllTags() then we need to rebuild the URL hash
    if (typeof(show) === "undefined" && typeof(override) === "undefined") {
        // Build URL hashtag in case user copies/pastes the URL
        // console.groupCollapsed('filterTag'); buildUrlHash(); console.groupEnd();
        buildUrlHash();
    }
}



function filterAllTags(show) {
    let tagFilters = document.querySelectorAll('header #tagFilters .tagFilter a');

    if (show === true) {
        // Activate all other filter links, except HIDE ALL
        for (let i = 0; i < tagFilters.length; i++) {
            if (tagFilters[i].id === 'tag-ALL' || tagFilters[i].id === 'tag-NONE' || tagFilters[i].id === 'tag-headlines') {
                continue;
            }

            // Activate all timeline cards
            filterTag(tagFilters[i].id.substr(4), true);

            // Update the activity of the filter link
            tagFilters[i].classList.remove('inactive');
            tagFilters[i].classList.add('active');

            // Update the activity of the filter link's parent DIV
            tagFilters[i].parentElement.classList.remove('inactive');
            tagFilters[i].parentElement.classList.add('active');
        }

        // Update the filter links
        document.getElementById('tag-ALL').classList.add('active');
        document.getElementById('tag-ALL').classList.remove('inactive');
        document.getElementById('tag-NONE').classList.add('inactive');
        document.getElementById('tag-NONE').classList.remove('active');

        // Update the filter links' parent DIVs
        document.getElementById('tag-ALL').parentElement.classList.add('active');
        document.getElementById('tag-ALL').parentElement.classList.remove('inactive');
        document.getElementById('tag-NONE').parentElement.classList.add('inactive');
        document.getElementById('tag-NONE').parentElement.classList.remove('active');
    } else {
        // Inactivate all other filter links
        for (let i = 0; i < tagFilters.length; i++) {
            if (tagFilters[i].id === 'tag-ALL' || tagFilters[i].id === 'tag-NONE' || tagFilters[i].id === 'tag-headlines') {
                continue;
            }

            // Deactivate all timeline cards
            filterTag(tagFilters[i].id.substr(4), false);

            // Update the activity of the filter link
            tagFilters[i].classList.remove('active');
            tagFilters[i].classList.add('inactive');

            // Update the activity of the filter link's parent DIV
            tagFilters[i].parentElement.classList.remove('active');
            tagFilters[i].parentElement.classList.add('inactive');
        }

        // Update the filter links
        document.getElementById('tag-ALL').classList.remove('active');
        document.getElementById('tag-ALL').classList.add('inactive');
        document.getElementById('tag-NONE').classList.remove('inactive');
        document.getElementById('tag-NONE').classList.add('active');

        // Update the filter links' parent DIVs
        document.getElementById('tag-ALL').parentElement.classList.remove('active');
        document.getElementById('tag-ALL').parentElement.classList.add('inactive');
        document.getElementById('tag-NONE').parentElement.classList.remove('inactive');
        document.getElementById('tag-NONE').parentElement.classList.add('active');
    }

    // Now handle showing the appropriate timeline dates next to the cards
    showFirstCardDatestamp();

    // And align the card to the appropriate side of the timeline
    alignCardToTimeline();

    // Build URL hashtag in case user copies/pastes the URL
    console.groupCollapsed('filterAll'); buildUrlHash(); console.groupEnd();

    // Scroll back to the top
    scrollToTop();
}


// This function shows/hides timeline event bodies so that only headlines are displayed/hidden
function hideHeadlines(show) {
    const headlines = document.getElementById("tag-headlines");
    let events = document.querySelectorAll("article main.content");
    let status = !headlines.classList.contains("inactive");

    // If an override is provided, say from reading the URL hash, then trick the logic to honor show's desired outcome
    if (typeof(show) !== "undefined") {
        status = !(show === 'active');
    }

    for (let i = 0; i < events.length; i++) {
        if (status) { // Show all event data
            // Expand the timeline event card to original size
            events[i].style.paddingTop = "10xp";
            events[i].style.paddingBottom = "30px";
            events[i].parentElement.querySelector("i.icon").style.top = "calc(50% - 20px)";

            // Show the event body and footer
            events[i].querySelectorAll("section p, section ol, section ul, footer").forEach((el) => { el.classList.remove("hidden") });

            // Restore the share button & headlines positions
            events[i].querySelector("i.share").style.left = "-20px";
            events[i].querySelector("i.share").style.top = "8px";
            events[i].querySelector("h2").style.marginTop = "0";

            // Update the tag filter buttons
            headlines.parentElement.classList.remove("active");
            headlines.parentElement.classList.add("inactive");
            headlines.classList.remove("active");
            headlines.classList.add("inactive");
        } else { // Show ONLY the event headlines
            // Shrink the timeline event card
            events[i].style.paddingTop = "0";
            events[i].style.paddingBottom = "15px"; // min required for icon to fit within box
            events[i].parentElement.querySelector("i.icon").style.top = "calc(50% - 25px)"; // min required for icon to fit within box

            // Hide the event body and footer
            events[i].querySelectorAll("section p, section ol, section ul, footer").forEach((el) => { el.classList.add("hidden") });

            // Move the share button & headlines out of the way of the icon circular border
            events[i].querySelector("i.share").style.left = "-13px";
            events[i].querySelector("i.share").style.top = "15px";
            events[i].querySelector("h2").style.marginTop = "7px";

            // Update the tag filter buttons
            headlines.parentElement.classList.remove("inactive");
            headlines.parentElement.classList.add("active");
            headlines.classList.remove("inactive");
            headlines.classList.add("active");
        }
    }

    // Build URL hashtag in case user copies/pastes the URL only if override isn't set (prevents -1 looping)
    if (typeof(show) === "undefined") {
        console.groupCollapsed('filterAll');
        buildUrlHash();
        console.groupEnd();
    }
}


// Hide all timeline events except those tagged with $tag
// Scroll to the new position of the timeline event the user clicked the tag from
function showOnly(tag, el) {
    filterAllTags(false);
    filterTag(tag);
    el.scrollIntoView(true);
}


// This function ensures there is always a date visible on the first visible card for a date
// It will also alternate the cards left/right of the timeline
function showFirstCardDatestamp() {
    let articles = document.querySelectorAll('.timeline article'),
        date = '',
        visible = false;

    // Iterate all timeline articles to query their visibility
    for (let i = 0; i < articles.length; i++) {
        // If we're iterating over a new date
        if (articles[i].querySelector('time').dateTime !== date) {
            // If the first card is visible
            if (!articles[i].classList.contains('hidden')) {
                // Then make sure the datestamp is visible, set the date var, and iterate further
                articles[i].querySelector('time').classList.remove('hidden');

                date = articles[i].querySelector('time').dateTime;
                visible = true;

                continue;
            }

            // Otherwise the first card is hidden and we need to check subsequent cards of the same date
            visible = false;

            continue;
        }

        // We're now iterating over another card of the same date
        if (visible) {
            // Then make sure the datestamp is visible, set the date var, and iterate further
            articles[i].querySelector('time').classList.add('hidden');
        } else {
            // Otherwise hide the datestamp
            articles[i].querySelector('time').classList.remove('hidden');
        }
    }
}


// This function ensures there is always a date visible on the first visible card for a date
// It will also alternate the cards left/right of the timeline
function alignCardToTimeline() {
    let articles = document.querySelectorAll('.timeline article'),
        date = '',
        left = false;

    // Iterate all timeline articles to query their visibility
    for (let i = 0; i < articles.length; i++) {
        // If we're iterating over a new date
        if (articles[i].querySelector('time').dateTime !== date) {
            // If the first card is visible
            if (!articles[i].classList.contains('hidden')) {
                // Then we need to adjust the card's positioning relative to the timeline
                date = articles[i].querySelector('time').dateTime;
                left = !(left);

                if (left) {
                    articles[i].classList.remove('right');
                    articles[i].classList.add('left');
                } else {
                    articles[i].classList.remove('left');
                    articles[i].classList.add('right');
                }
            }

            // Otherwise the first card is hidden and we need to check subsequent cards of the same date
            continue;
        }

        // We're now iterating over another card of the same date
        if (left) {
            articles[i].classList.remove('right');
            articles[i].classList.add('left');
        } else {
            articles[i].classList.remove('left');
            articles[i].classList.add('right');
        }
    }
}



// Prevent <a href="#"> links from scrolling to top of page
// https://medium.com/@jacobwarduk/how-to-correctly-use-preventdefault-stoppropagation-or-return-false-on-events-6c4e3f31aedb
let links = document.getElementsByTagName("a");
for (let i = 0; i < links.length; i++) {
    links[i].addEventListener('click', (event) => {
        if (links[i].href === '#' || links[i].href === 'https://gmetimeline.com/#' || links[i].href === (window.location.href + "#")) {
            event.preventDefault();
        }
    });
}


// https://stackoverflow.com/a/16270434
function isElementInViewport(el) {
    let rect = el.getBoundingClientRect();

    return rect.bottom > 0 &&
        rect.right > 0 &&
        rect.left < (window.innerWidth || document.documentElement.clientWidth) /* or $(window).width() */ &&
        rect.top < (window.innerHeight || document.documentElement.clientHeight) /* or $(window).height() */;
}


// Make actively viewed year stickied to top of page
// https://medium.com/@christinagreene5/what-you-need-to-know-about-scroll-then-fix-17ce50364c15
function stickyActiveYear() {
    // console.groupCollapsed('stickyActiveYear(event) called');

    // Loop through .newYear elements until we find the first one with a positive y-coordinate, then sticky it
    let newYears = document.getElementsByClassName('newYear');
    for (let i = 0; i < newYears.length; i++) {
        let curYear = newYears[i];

        // console.group({
        //     i: i,
        //     visible: isElementInViewport(newYears[i]),
        //     parentVisible: isElementInViewport(newYears[i].parentElement),
        // });

        if (isElementInViewport(curYear)) {
            // console.log('The year itself is visible and is somewhere in the viewport, might need to sticky it soon');

            if (curYear.getBoundingClientRect().top <= curYear.children[0].offsetHeight) {
                // console.debug({top: curYear.getBoundingClientRect().top, childOffsetHeight: curYear.children[0].offsetHeight});
                // console.log('The year is within the bounds of the top, time to sticky it to the top and add .active class');

                if (!curYear.children[0].classList.contains('hideAll')) {
                    // Only sticky it if the timeline cards for this year are not hidden
                    curYear.classList.add('active');
                    curYear.children[0].classList.add('active');

                    // Update the jump to year drop-down too
                    document.getElementById('jumpYear').value = curYear.children[0].children[0].dateTime;
                }
            } else if (curYear.getBoundingClientRect().top > curYear.children[0].getBoundingClientRect().top) {
                // console.debug({
                //     curYearTop: curYear.getBoundingClientRect().top,
                //     curYearChildTop: curYear.children[0].getBoundingClientRect().top});
                // console.log('The year is above the original position, indicating we passed it and should deactivate it');

                curYear.classList.remove('active');
                curYear.children[0].classList.remove('active');

                // See if there is a preceding year, if so then sticky it
                if (newYears[i-1] !== undefined) {
                    newYears[i-1].classList.add('active');
                    newYears[i-1].children[0].classList.add('active');

                    // Update the jump to year drop-down too
                    document.getElementById('jumpYear').value = newYears[i-1].children[0].children[0].dateTime;
                }
            }
        } else if (curYear.children[0].classList.contains('active') && isElementInViewport(curYear.parentElement)) {
            // console.log('Still active year, do nothing');
        } else if (isElementInViewport(curYear.parentElement)) {
            // console.log ('The element is visible but everything else is false, sticky this year');

            newYears[i].classList.add('active');
            newYears[i].children[0].classList.add('active');

            // Update the jump to year drop-down too
            document.getElementById('jumpYear').value = newYears[i].children[0].children[0].dateTime;
        } else {
            // console.log("Isn't in viewport, doesn't contain .active, parent isn't in viewport, remove .active from the newYear");

            curYear.classList.remove('active');
            curYear.children[0].classList.remove('active');
        }

        // console.groupEnd();
    }

    // Lastly, check if we're at the bottom of the page and update the drop down to reflect The Start
    // First get the last timeline event and see if that's within view, make sure that it isn't but that the bottom is in view
    let stickyYear = newYears[newYears.length-1].querySelector("time");
    if (isElementInViewport(document.getElementById("bottom")) && !isElementInViewport(stickyYear)) {
        document.getElementById('jumpYear').value = "moon";
    }

    // console.groupEnd();
}
// Call stickyActiveYear() every time the document is scrolled, also handles page up/down keystrokes
document.addEventListener('scroll', stickyActiveYear);


function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

function scrollToBottom() {
    document.getElementById("bottom").scrollIntoView(true);
}


// Load the remainder of images not initially loaded due to lazy="loading"
// Ideally this would be called after the page has rendered, but may be called if the year drop-down is activated
// This is needed so that jumps in the timeline are accurate, because lazy images not loaded before the jumps prevent
// accurate jumping
window.onload = () => {
    let lazyImages = document.querySelectorAll("img");

    console.log('Window loaded, loading remaining images');

    // Delayed loading so that fontawesome can load first
    setTimeout(() =>
        {
            for (let img of lazyImages) {
                if (img.loading !== "lazy") continue;

                img.loading = "eager"; // Force image to load immediately
            }
        },
        1000);
}
