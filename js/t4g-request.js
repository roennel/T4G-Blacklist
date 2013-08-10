var t4gRequest = new Class
({
  initialize: function()
  {
    this.aliases = new Hash;
    
  },
  
  prepare: function(alias, data)
  {
    var url = '';
    var vars;
    
    if(this.aliases.get(alias).contains('?'))
    {
      var spl = this.aliases.get(alias).split('?');
      
      url+= spl[0];
      vars = spl[1].substitute(data);
    }
    else
    {
      url+= this.aliases.get(alias);
    }
    
    if(url.substring(0, 1) == '/')
    {
      url = t4g.url.buildBase(t4g.url.mainHost) + url.substring(1, url.length);
    }
    else
    {
      url = t4g.url.buildBase() + url;
    }
    
    return [url, vars];
  },
  
  get: function(alias, data, callback, method)
  {
    var prepare = this.prepare(alias, data);
    
    new Request.JSON
    ({
      url: prepare[0],
      method: (!method ? 'get' : method),
      onSuccess: (!callback ? function() {} : callback)
    }).get(prepare[1])
  },
  
  post: function(alias, data, callback, method)
  {
    var prepare = this.prepare(alias, data);
    
    new Request.JSON
    ({
      url: prepare[0],
      method: (!method ? 'get' : method),
      onSuccess: (!callback ? function() {} : callback)
    }).post(prepare[1])
  },
  
  html: function(alias, data, callback, method)
  {
    var prepare = this.prepare(alias, data);
    
    new Request.HTML
    ({
      url: prepare[0],
      method: (!method ? 'get' : method),
      onSuccess: (!callback ? function() {} : callback)
    }).get(prepare[1])
  }
});
