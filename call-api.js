var settings = {
    "url": "http://localhost/ContadorLascondes/apiContadoresCamaraM.php",
    "method": "GET",
    "timeout": 0,
    "headers": {
      "Authorization": "Basic TGFzQ29uZGVzOldpdDIwMjQ="
    },
  };
  
  $.ajax(settings).done(function (response) {
    console.log(response);
  });