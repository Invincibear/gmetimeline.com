/* higher density = fewer stars */
let windowArea = Math.round(window.innerWidth * window.innerHeight),
    density = (windowArea > 309414) ? 40000 : 50000, // Generate fewer stars on small mobile devices
    maxStars = Math.round(windowArea / density);

for (let i = 0; i < maxStars; i++) {
    let el = document.createElement('div'),
        rand = Math.ceil(Math.random() * 10),
        animation = Math.ceil(Math.random() * 10);

    el.classList.add("bgStar");

    // Half the generated starts will be animated to reduce CPU-intensive calculations on weaker machines
    if (rand > 4) {
        // el.classList.add("starAnimation" + animation);
        el.classList.add("starSize" + animation);
    }

    el.style.top = Math.ceil(Math.random() *  window.innerHeight) + "px";
    el.style.left = Math.ceil(Math.random() *  window.innerWidth) + "px";
    el.innerHTML = '<i class="icon far fa-gem"></i>';

    document.getElementById("bgStars").appendChild(el);
}