//Get the pop-up window
var modal = document.getElementById("popUpLogin");

// Get the button that opens the window
var btn = document.getElementById("loginButton");

// Get the <span> element that closes the modal, x symbol
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the pop-up window
btn.onclick = function() {
    modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}
// When the user clicks anywhere outside of the window, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}


// When the user enters data, it checks whether the input was not empty
function validate() {
    var $valid = true;
    document.getElementById("loginInput").innerHTML = "";
    document.getElementById("passwordInput").innerHTML = "";

    var userName = document.getElementById("loginInput").value;
    var password = document.getElementById("passwordInput").value;
    if(userName == "")
    {
        document.getElementById("loginInput").innerHTML = "required";
        $valid = false;
    }
    if(password == "")
    {
        document.getElementById("passwordInput").innerHTML = "required";
        $valid = false;
    }
    return $valid;
}

