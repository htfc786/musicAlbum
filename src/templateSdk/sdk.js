function getData(){
  var getQueryVariable = (variable) => {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return false;
  }
  var getImageName = (url) => {
    var index1 = url.indexOf("?");
    if (index1 != -1) {
      url = url.substr(0, index1);
    }
    return url.toString().substr(url.lastIndexOf("/") + 1);
  }
  var requestHost = getQueryVariable("requestHost");
  var albumName = getQueryVariable("albumName");
  var albumId = getQueryVariable("albumId");

  var fd = new FormData();
  fd.append("albumId", albumId);
  var xhr = new XMLHttpRequest()
  xhr.open("post", requestHost, false);
  xhr.send(fd)

  var data = JSON.parse(xhr.response).data;

  //name
  var name = decodeURIComponent(albumName);
  //image
  var image = [];
  for (i = 0; i < data.length; i++) {
    var imageUrl = data[i].photoUrl;
    image.push(imageUrl);
  }
  //text
  var text = {};
  for (i = 0; i < data.length; i++) {
    var imageText = data[i].photoText;
    if (!imageText){
      continue;
    }
    var imageUrl = data[i].photoUrl;
    var imageName = getImageName(imageUrl);
    text[imageName] = imageText;
  }
  return {
    name: name,
    image: image,
    text: text,
  }
}