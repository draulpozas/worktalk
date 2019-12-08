var chat_id = document.getElementsByTagName('meta')[1].content;

function sendmsg(){
    console.log(chat_id);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let chat = document.getElementById("content");
            chat.innerHTML = this.responseText;
            scrollChat();
        }
    };
    let t = document.getElementById("input").value;
    document.getElementById("input").value = '';
    xhttp.open("GET", "./../view/php/msg.php?msg="+t+"&chat_id="+parseInt(chat_id, 10), true);
    xhttp.send();
}

function showMsgDetails(id){
    let s = "details";
    let details = document.getElementById(s.concat(id));
    details.style.display = (details.style.display == "none" ? "block" : "none");
}

function scrollChat(){
    let chat = document.getElementById('content');
    chat.scrollTo(0, chat.scrollHeight);
}

function e(event){
    if (event.key == 'Enter') {
        sendmsg(chat_id);
    }
}

function chatRefresh(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let chat = document.getElementById("content");
            let initH = chat.scrollHeight;

            if (count(chat.innerHTML) != count(this.responseText)) {
                console.log('chat refresh');
                chat.innerHTML = this.responseText;
                chat.scrollBy(0, (chat.scrollHeight-initH));
            }
        }
    };
    xhttp.open("GET", "./../view/php/refresh.php?chat_id="+parseInt(chat_id, 10), true);
    xhttp.send();
}

function chatInit(){
    scrollChat();
    setInterval(chatRefresh, 3500);
}

const count = (str) => {
    const re = /class="msg"/g
    return ((str || '').match(re) || []).length
  }

// window.onload = scrollChat;
window.onload = chatInit;