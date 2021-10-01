// Respect OS's desired light/dark theme setting
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
if (prefersDarkScheme.matches) {
    changeTheme('dark');
} else {
    changeTheme('light');
}

/* https://stackoverflow.com/a/19844696 */
function changeTheme(preference) {
    console.group('changeTheme(preference) called', {preference: preference});

    let stylesheet = 'https://gmetimeline.com/css/' + preference + '.css',
        oldLink = document.getElementsByTagName("link").item(5),
        newLink = document.createElement("link");

    newLink.setAttribute("rel", "stylesheet");
    newLink.setAttribute("type", "text/css");
    newLink.setAttribute("href", stylesheet);
    // newLink.setAttribute("id", "theme");

    document.getElementsByTagName("head").item(0).replaceChild(newLink, oldLink);

    console.groupEnd();
    return false;
}


// Change theme to dark mode if user is in night/early morning hours
/*function timeToChangeTheme() {
    let date = new Date();

    if (date.getHours() >= 20 || date.getHours() <= 8) {
        changeTheme('dark');
    } else {
        changeTheme('light');
    }
}*/


// Flash the theme selection 4 times on page load
// https://stackoverflow.com/a/22252268
function flashChangeTheme() {
    // return;

    let elem = document.getElementById("changeTheme"),
        count = 1,
        intervalId = setInterval(function() {
        if (elem.style.visibility === 'hidden') {
            elem.style.visibility = 'visible';
            if (count++ === 4) {
                clearInterval(intervalId);
            }
        } else {
            elem.style.visibility = 'hidden';
        }
    }, 400);
}


// timeToChangeTheme(); // Now going based off of OS setting
flashChangeTheme();


function toggleAnimations(action) {
    const linkText = document.getElementById('animationLink');
    const stars = document.getElementsByClassName('bgStar');

    const rocket = document.getElementsByClassName('rocket-body')[0];
    const flame = document.getElementsByClassName('exhaust-flame')[0];
    const fumes = document.getElementsByClassName('exhaust-fumes')[0].children;

    let animated = (linkText.innerText === 'stop animations');

    if (animated || action === 'stop') {
        linkText.innerText = 'start animations';

        rocket.classList.add('paused');
        flame.classList.add('paused');
        for (let star of stars) { star.classList.add('paused'); }
        for (let fume of fumes) { fume.classList.add('paused'); }
    } else {
        linkText.innerText = 'stop animations';

        rocket.classList.remove('paused');
        flame.classList.remove('paused');
        for (let star of stars) { star.classList.remove('paused'); }
        for (let fume of fumes) { fume.classList.remove('paused'); }
    }
}
toggleAnimations('stop');




/* Remove top margin of theme selection DIV upon sufficient scrolling
document.getElementsByTagName('body')[0].onscroll = () => {
    let changeTheme = document.getElementById('changeTheme'),
        jumpToYear = document.getElementById('changeTheme'),
        top = window.pageYOffset || document.documentElement.scrollTop, // https://stackoverflow.com/a/14384091
        height = changeTheme.offsetHeight;

        if (top < height) {
            let newMarginTop;

            if ((height - top - 5) <= 0) {
                newMarginTop = 0;
            } else if ((height - top - 5) > 5) {
                newMarginTop = 5;
            } else {
                newMarginTop = (height - top - 5);
            }

            changeTheme.style.marginTop = newMarginTop + 'px';
            // jumpToYear.style.top = (25 - newMarginTop) + 'px';
        } else {
            changeTheme.style.marginTop = '0';
            // jumpToYear.style.top = '20px';
        }
};*/