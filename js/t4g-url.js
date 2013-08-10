var t4gURL = new Class
({
	initialize: function()
	{
	  this.obj = self;
	  
		this.hash = new Hash;
		this.query = new Hash;
		this.opt = [];
		
		this.protocol = 'http';
		this.host;
		this.mainHost;
		this.port = 80;
		this.base;
		
		this.lang;
		this.controller;
		this.action;
	
	  this.game;
	},
	
	setLanguage: function(lang)
	{
	  this.lang = lang;
	},
	
	getLanguage: function()
	{
	  return this.lang;
	},
	
	setController: function(controller)
	{
	  this.controller = controller;
	},
	
	getController: function()
	{
	  return this.controller;
	},
	
	setAction: function(action)
	{
	  this.action = action;
	},
	
	getAction: function()
	{
	  return this.action;
	},
	
	setGame: function(game)
	{
	  this.game = game;
	},
	
	getGame: function()
	{
	  return this.game;
	},
	
	buildBase: function(host)
	{
	  var url = this.protocol + '://';
	  url+= this.game ? this.game + '.' : '';
    url+= host ? host : this.host;
    url+= this.port != 80 ? ':' + this.port : '';
    url+= this.base;
    url+= this.lang + '/';
    
    return url;
	},
	
	buildQuery: function()
	{
	  var items = [];
	  
	  this.query.each(function(value, key)
	  {
	    items.push(key + '=' + value);
	  });
	  
	  return (this.query.getLength() > 0 ? '?' : '') + items.implode('&');
	},
	
	build: function()
	{
	  var url = this.buildBase();
	  url+= this.controller + (this.action ? '/' : '');
	  url+= this.action;
	  
	  this.opt.each(function(item)
	  {
	    url+= '/' + item;
	  });
	  
	  url+= this.buildQuery();
	  
	  return url;
	},
	
	redirect: function()
	{
	  this.obj.location.href = this.build();
	}
});
