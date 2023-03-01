// document.getElementById('inputfile').addEventListener('change', function() {
//     var fr=new FileReader();
//     fr.onload=function(){
//         alert("loaded");
//         document.getElementById('output').textContent=fr.result;
//         console.log(this.files[0]);
//     }
    
//     // fr.readAsText(this.files[0]);
// })

function readFile(input) {
  let file = input.files[0];

  let reader = new FileReader();

  reader.readAsText(file);

  reader.onload = function() {
    console.log(reader.result);
    createCookie("file", reader.result, "1");
    createCookie("path", "1", "1");
    location.reload()
  };

  reader.onerror = function() {
    console.log(reader.error);
  };
  
}

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
async function process() {
    
    let allow = getCookie("allow");
    if (allow == "1") {
    let path = getCookie("path");
    console.log(path);
    let cells = path.split('.');
    for(let i = 0; i < cells.length - 1; i++) {
        await sleep(100);
        console.log("cell" + cells[i]);
        console.log(document.getElementById("cell" + cells[i]));
        document.getElementById("cell" + cells[i]).style.backgroundColor = "red";
    // 	document.write(elem + '<br>');
    }
}
}
process();

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
