function usercheck(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let usercheck = document.getElementById('usercheck');
            usercheck.innerHTML = this.responseText;
        }
    };
    let username = document.getElementById('username').value;
    xhttp.open("GET", "./../view/php/checkUsername.php?username="+username, true);
    xhttp.send();
}

function passwdcheck(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let passwdcheck = document.getElementById('passwdcheck');
            passwdcheck.innerHTML = this.responseText;
        }
    };
    let passwd = document.getElementById('passwd').value;
    let passwd2 = document.getElementById('passwd2').value;
    xhttp.open("GET", "./../view/php/checkPasswd.php?passwd="+passwd+"&passwd2="+passwd2, true);
    xhttp.send();
}