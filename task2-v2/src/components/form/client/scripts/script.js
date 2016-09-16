function ajaxRequest(element, query, url) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     element.innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", url + '?' + query, true);
  xhttp.send();
}

function ajax(form) {
  var inputs = form.getElementsByTagName('input');
  var query = '';
  for (var i = 0, len = inputs.length; i < len; i++) {
    if (inputs[i].value !== 'Submit') {
      query += inputs[i]['name'] + '=' + inputs[i].value + '&';
    }
  }
  var errorDiv = document.getElementById('response');
  ajaxRequest(errorDiv, query, '');
}
