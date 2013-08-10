// Main Object
var t4g = new t4gMain;

// Request Aliases
t4g.request.aliases = new Hash
({});

function prepare(str)
{
  str = str.replace('+', '[___]');
  str = str.replace('#', '[____]');
  
  return str;
}
