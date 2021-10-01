function openTips() {
    document.getElementById("tips").classList.remove("hidden");
}

function submitTips() {
    let tip = document.getElementById("tip").value;

    // Don't bother with an empty tip, should prevent 400 error
    if (!tip.length) return closeTips();

    // Start a new AJAX request
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "tips.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            switch (xhr.status) {
                case 400:
                    // POSTed tip is empty
                    break;
                case 429:
                    // User is rate-limited
                    break;
                case 500:
                    // Error sending email
                    break;
                case 200:
                default:
                    // Tip successfully sent
                    showToast('Thanks for the tip!');
                    break;
            }

            console.log({xhr:xhr, rt:xhr.responseText});
        }
    }
    xhr.send('tip=' + encodeURIComponent(tip));

    closeTips();
}


function closeTips() {
    document.getElementById("tips").classList.add("hidden");
}