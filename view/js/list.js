function refreshChats(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let chats = document.getElementById("chatlist");
            chats.innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "./../view/php/refreshList.php", true);
    xhttp.send();
}

function autoRefresh(){
    setInterval(refreshChats, 3500);
}

window.onload = autoRefresh;