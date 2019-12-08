function checkUsername(){
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