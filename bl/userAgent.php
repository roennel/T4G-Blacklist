<!DOCTYPE html>
<html>
<head>
  <title>Browser Infos</title>
</head>
<body>
<div>userAgent: <span id="userAgent"></span></div>
<div>appName: <span id="appName"></span></div>
<div>appCodeName: <span id="appCodeName"></span></div>
<div>appVersion: <span id="appVersion"></span></div>
<div>platform: <span id="platform"></span></div>

<script type="text/javascript">
document.getElementById('userAgent').innerHTML = navigator.userAgent;
document.getElementById('appName').innerHTML = navigator.appName;
document.getElementById('appCodeName').innerHTML = navigator.appCodeName;
document.getElementById('appVersion').innerHTML = navigator.appVersion;
document.getElementById('platform').innerHTML = navigator.platform;
</script>

</body>
</html>