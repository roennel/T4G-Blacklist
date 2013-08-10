
var alxToolkit = new Hash(
{
	config: 
	{
		paths: 
		{
			mooToolsCore: 'http://js.alchemical.ch/mootools/mootools-core.js',
			mooToolsMore: 'http://js.alchemical.ch/mootools/mootools-more.js'
		}
	},
	
	injectScript: function(src)
	{
		var script = document.createElement('script');
	
		script.setAttribute('type', 'text/javascript');
		script.setAttribute('src', src);
	
		document.getElementsByTagName('head')[0].appendChild(script);
	}
});

var alx = new alxToolkit;

if(!Browser)
{
	alx.injectScript(alx.config.paths.mooToolsCore);
}

if(!MooTools.More)
{
	alx.injectScript(alx.config.paths.mooToolsMore);
}