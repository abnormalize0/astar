function createCookie(name, value, days) {
  var expires;
    
  if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toGMTString();
  }
  else {
      expires = "";
  }
    
  document.cookie = escape(name) + "=" + 
      escape(value) + expires + "; path=/";
}

function getCookie(cname) {
let name = cname + "=";
let decodedCookie = decodeURIComponent(document.cookie);
let ca = decodedCookie.split(';');
for(let i = 0; i <ca.length; i++) {
  let c = ca[i];
  while (c.charAt(0) == ' ') {
    c = c.substring(1);
  }
  if (c.indexOf(name) == 0) {
    return c.substring(name.length, c.length);
  }
}
return "";
}
function setpoint(x,y) {
  let test = getCookie("point1x");
  if (test == "-1") {
      createCookie("point1x", x, "1");
      createCookie("point1y", y, "1");
  } else {
      createCookie("point2x", x, "1");
      createCookie("point2y", y, "1");
      location.reload();
  }
}
