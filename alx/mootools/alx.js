
var injectScript = function(src)
{
	var script = document.createElement('script');
	
	script.setAttribute('type', 'text/javascript');
	script.setAttribute('src', src);
	
	document.getElementsByTagName('head')[0].appendChild(script);
};

if(!Browser)
{
	injectScript('http://js.alchemical.ch/mootools/mootools-core.js');
	injectScript('http://js.alchemical.ch/mootools/mootools-more.js');
}

var alxToolkit = $H(
{
	version: 0.1,
	
	config: 
	{
		application:
		{
			
		},
		paths: 
		{
			
		}
	},
	
	setApplicationConfig: function()
	{
		Locale.use(this.config.application.locale);
	}
});

var alx = new alxToolkit;
