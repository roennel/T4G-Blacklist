<!DOCTYPE html>
<html>
<head><?php

if(alxRequestHandler::getController() == 'admin')
{
  $t = ts('nav_' . alxRequestHandler::getController()) . ' > ' . ts('nav_' . alxRequestHandler::getController() . '_' . alxRequestHandler::getAction());
}
else
{
  $t = ts('nav_' . alxRequestHandler::getController());
}

$this->insertTitle('T4G Blacklist' . (!empty($t) ? ' > ' . $t : ''));

$this->insertCSS('reset');
$this->insertCSS('t4g_bl_2012');

$this->insertJS('mootools');
$this->insertJS('prototypes');
$this->insertJS('sorttable');

$this->insertJS('t4g-url');
$this->insertJS('t4g-language');
$this->insertJS('t4g-request');

$this->insertJS('t4g');
$this->insertJS('t4g-init');

?>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php if(@$_GET['m']): ?>
<link rel="stylesheet" type="text/css" href="/css/t4g_bl_m.css?<?= rand() ?>"/>
<?php endif ?>
<link rel="shortcut icon" href="/favicon.png" />
<link href="http://fonts.googleapis.com/css?family=Strait" rel="stylesheet" type="text/css">
<?php if(alxRequestHandler::getController() == 'stats'): ?>
<style>
#container
{
  width: 1200px !important;
}
</style>
<?php endif ?>
<script type="text/javascript">

t4g.url.host = 'blacklist.tools4games.com';
t4g.url.base = '/';
t4g.url.lang = '<?php echo getLang() ?>';
t4g.url.controller = '<?php echo alxRequestHandler::getController() ?>';
t4g.url.action = '<?php echo alxRequestHandler::getAction() != 'index' ? alxRequestHandler::getAction() : '' ?>';
t4g.url.game = '<?php echo @alxApplication::getConfigVar('id', 'game') ?>';

<?php
  foreach($_GET as $key => $val)
  {
    $valid = true;
    
    switch($key)
    {
      case 'lang':
      case 'controller':
      case 'action':
        $valid = false;
      break;
    }
    
    switch(alxRequestHandler::getController())
    {
      case 'servers':
      case 'appeal':
      case 'submit':
        $valid = false;
      break;
    }
    
    if(!$valid) continue;
    
    echo "t4g.url.query.set('{$key}', '{$val}');\n";
  }
?>

window.addEvent('domready', function()
{
  t4g.language.languages = ['en', 'de'];
  t4g.language.active = '<?php echo getLang() ?>';
  
<?php 
    foreach($GLOBALS['t4gLang'] as $key => $val)
    {
      echo "  t4g.language.items.set('{$key}', '{$val}');\n";
    }
  ?>
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36889284-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>

<div id="container" style="margin-top: 21px">
  
  <div class="logo"></div>
  
  <?php if(!isLogged() and (alxRequestHandler::getController() . '_' . alxRequestHandler::getAction()) != 'account_login'): ?>
  <div class="login sub">
    
    <h2><?php t('login') ?></h2>
    
    <table class="input">
      <tbody>
        <tr>
          <td><?php t('username') ?></td>
          <td>
            <input type="text" id="authUsername" />
          </td>
          <td></td>
        </tr>
        <tr>
          <td><?php t('password') ?></td>
          <td>
            <input type="password" id="authPassword" />
            <a href="/en/account/login?forgotPassword=1" style="font-size: 10pt; position: absolute; margin-top: 1px" tabindex="-1"><?php t('forgotPw') ?>?</a>
          </td>
          <td>
            <input type="button" id="auth" value="<?php t('login') ?>" />
          </td>
        </tr>
      </tbody>
    </table>
    
    <script type="text/javascript">
      window.addEvent('domready', function()
      {
        var auth = function()
        {
          $$('table.input input').removeClass('error');
          
          var username = $('authUsername').get('value');
          var pwd = $('authPassword').get('value');
          
          if(username == '')
          {
            $('authUsername').addClass('error');
            return;
          }
          
          if(pwd == '')
          {
            $('authPassword').addClass('error');
            return;
          }
          
          $('auth').disabled = true;
          
          new Request.JSON
          ({
            url: '/en/auth/login',
            onSuccess: function(response)
            {
              if(response.state)
              {
                t4g.url.controller = 'account';
                t4g.url.action = '';
                t4g.url.redirect();
              }
              else
              {
                t4g.url.controller = 'account';
                t4g.url.action = 'login';
                t4g.url.query.set('username', username);
                t4g.url.query.set('loginError', response.error);
                t4g.url.redirect();
              }
            }
          }).get('username=' + encodeURIComponent(username) + '&pwd=' + encodeURIComponent(pwd));
        };
        
        $('authPassword').addEvent('keydown', function(event){
          if(event.key == 'enter') auth();
        });
        
        $('auth').addEvent('click', auth);
      });
    </script>
    
  </div>
  <?php endif ?>
  
  <div class="nav">
    <div class="nav-inner">
      <div class="nav-items">
        <div onclick="javascript:t4g.url.controller = 'home'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_home') ?></a>
        </div>
        
        <?php if(!isLogged()): ?>
        <div onclick="javascript:t4g.url.controller = 'register'; t4g.url.action = 'server'; t4g.url.redirect()">
          <a href="#"><?php t('nav_register') ?></a>
        </div>
        <?php endif ?>
        
        <div onclick="javascript:t4g.url.controller = 'submit'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_submit') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'servers'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_servers') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'search'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_search') ?></a>
        </div>
      
        <?php if(!isLogged()): ?>
        <div onclick="javascript:t4g.url.controller = 'appeal'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_appeal') ?></a>
        </div>
        <?php endif ?>
        
        <div onclick="javascript:t4g.url.controller = 'status'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_status') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'team'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_team') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'alliance'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_alliance') ?></a>
        </div>

        <div onclick="javascript:t4g.url.controller = 'ranking'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_ranking') ?></a>
        </div>
        
        <div onclick="window.open('http://forum.tools4games.com')">
          <a href="#"><?php t('nav_forum') ?></a>
        </div>
        
        <div style="padding: 5px;padding-top: 10px;border-right: none !important; background:transparent !important">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
    ">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>


        </div>
        <!--
        <div class="noHover flags">
          <div class="flag flag_en" onclick="t4g.url.lang = 'en'; t4g.url.redirect()" title="English"></div>

          <div class="flag flag_de" onclick="t4g.url.lang = 'de'; t4g.url.redirect()" title="Deutsch"></div>
        </div>
        -->
      </div>
    </div>
  </div>
  
  <?php if(isLogged()): ?>
  <div class="nav navServerAdmin">
    <div class="nav-inner">
      <div class="nav-items">
        <div class="admin" onclick="javascript:t4g.url.controller = 'admin'; t4g.url.action = 'servers'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_servers') ?></a>
        </div>
        
        <div class="admin" onclick="javascript:t4g.url.controller = 'admin'; t4g.url.action = 'addServer'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_addServer') ?></a>
        </div>

        <div class="admin" onclick="javascript:t4g.url.controller = 'account'; t4g.url.action = ''; t4g.url.redirect()">
          <a href="#"><?php t('nav_account') ?></a>
        </div>
        
        <div class="admin" onclick="javascript:t4g.url.controller = 'auth'; t4g.url.action = 'logout'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_logout') ?></a>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  
  <?php if(isLogged() && isMod()): ?>
  <div class="nav navMod">
    <div class="nav-inner">
      <div class="nav-items">
        <div onclick="javascript:t4g.url.controller = 'modPanel'; t4g.url.action = 'submissions'; t4g.url.query.set('hideVoted', '1'); t4g.url.redirect()">
          <a href="#"><?php t('nav_mod_submissions') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'modPanel'; t4g.url.action = 'appeals'; t4g.url.redirect()">
          <a href="#"><?php t('nav_mod_appeals') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'modPanel'; t4g.url.action = 'search'; t4g.url.redirect()">
          <a href="#"><?php t('nav_search') ?></a>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  
  <?php if(isLogged() && isAdmin()): ?>
  <div class="nav navAdmin">
    <div class="nav-inner">
      <div class="nav-items">
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'submissions'; t4g.url.query.set('hideVoted', '1'); t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_finalSubmissions') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'appeals'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_finalAppeals') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'adminLog'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_log') ?></a>
        </div>
            
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'modLog'; t4g.url.redirect()">
          <a href="#"><?php t('nav_mod_log') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'userVoting'; t4g.url.redirect()">
          <a href="#"><?php t('nav_mod_userVoting') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'badSubmitters'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_badSubmitters') ?></a>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  
  <?php if(isLogged() && (isAdmin() or isMod())): ?>
  <div class="nav navAdminShared">
    <div class="nav-inner">
      <div class="nav-items">
        <!--
        <div onclick="javascript:t4g.url.controller = 'modPanel'; t4g.url.action = 'myVotedSubmissions'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_myVotedSubmissions') ?></a>
        </div>
        -->
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'doneSubmissions'; t4g.url.query.set('recent', '1'); t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_doneSubmissions') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'doneAppeals'; t4g.url.query.set('recent', '1'); t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_doneAppeals') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'backendStats'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_backendStats') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'kickLog'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_kickLog') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'modPanel'; t4g.url.action = 'stats'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_userStatsActivity') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'mostKickedUsers'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_mostKickedUsers') ?></a>
        </div>
        
        <div onclick="javascript:t4g.url.controller = 'adminPanel'; t4g.url.action = 'topSubmitters'; t4g.url.redirect()">
          <a href="#"><?php t('nav_admin_topSubmitters') ?></a>
        </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  
  <div class="content">
    
    <h1>
    <?php

      if(alxRequestHandler::getController() == 'admin')
      {
        $t = ts('nav_' . alxRequestHandler::getController()) . ' > ' . ts('nav_' . alxRequestHandler::getController() . '_' . alxRequestHandler::getAction());
      }
      else
      {
        $t = ts('nav_' . alxRequestHandler::getController());
      }

      echo $t;
      
    ?>
    </h1>
    
    <?php $this->insertContainer('content') ?>
  </div>

  <div style="margin-left: -130px;position: fixed; bottom: 120px; font-size: 8pt !important; color: #555 !important; -moz-transform: rotate(270deg);-webkit-transform:rotate(270deg);-o-transform:rotate(270deg);-ms-transform:rotate(270deg);" id="footer">
    <a href="/en/static/tos">Terms of Service</a> | <a href="/en/static/privacyPolicy">Privacy Policy</a> | &copy; 2012 Tools4Games
  </div>


</div>


<script src="http://tools4games.com/topbar.js"></script>

</body>
</html>