'use strict';

function openNav() {
    document.getElementById("screen_blind").style.display = "block";
    document.getElementById("side_panel").style.width = "21.875vw";
}

function closeNav() {
    document.getElementById("screen_blind").style.display = "none";
    document.getElementById("side_panel").style.width = "0";
}

function signOut() {
    location.href = "signout.php";
}

function setWebCookie(name, value, expiredays) {
    var todayDate = new Date();

    todayDate.setDate(todayDate.getDate() + expiredays);
    document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
}

function getWebCookie(Name) {
    var search = Name + "=";
    var offset;
    var end;

    if (document.cookie.length > 0) {
        offset = document.cookie.indexOf(search);

        if (offset != -1) {
            offset += search.length;
            end = document.cookie.indexOf(";", offset);

            if (end == -1) {
                end = document.cookie.length;
            }

            return unescape(document.cookie.substring(offset, end));
        }
    }
}

