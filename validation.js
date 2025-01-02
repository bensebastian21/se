function validateLogin() {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    
    if (!email || !password) {
        alert("Please fill in both fields.");
        return false;
    }
    return true;
}

function validateRegister() {
    var username = document.getElementBy
}