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