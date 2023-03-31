var div = document.getElementById("star")
var left =0
var updateLeft = function () {
    left = left + 10
    if (left > 250) {
        left = 0
    }
    div.style.left = left + "px"
}
setInterval(updateLeft, 100)