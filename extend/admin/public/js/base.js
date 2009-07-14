
var loader = document.createElement('img');
loader.src = BASE_URL + 'css/images/loader.gif';

function show_loader(selector)
{
  selector = $(selector);
  var html = selector.html();
  selector.html(loader);
  return html;
}
