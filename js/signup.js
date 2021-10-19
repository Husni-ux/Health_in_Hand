function check() {
   var lang = document.getElementById('lang').value;
  var male = document.getElementById('gender-male').checked;
  var female = document.getElementById('gender-female').checked;
  var checkBox = document.getElementById('terms').checked;
  if (male || female) {
    document.getElementById('error').innerHTML = "";
  } else {
    if (lang == 'en')
      document.getElementById('error').innerHTML = "please choose your gender";
    else
      document.getElementById('error').innerHTML = "اختر الجنس";
    document.getElementById('error').style.color = "red";
  }
  if (checkBox) {
    document.getElementById('error2').innerHTML = "";
  } else {
    if (lang == 'en')
      document.getElementById('error2').innerHTML = "please check this box";
    else
      document.getElementById('error2').innerHTML = "الرجاء تحديد هذا المربع";
    document.getElementById('error2').style.color = "red";
  }
  
}
navigator.geolocation.getCurrentPosition(function (position) {
  var lat = position.coords.latitude;
  var lng = position.coords.longitude;
  document.getElementById('lat').value = lat;
  document.getElementById('lng').value = lng;
})