<?php

/**
*
* @package - NV "who was here?"
* @version $Id$
* @copyright (c) nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
$activemenu = ' id="activemenu"';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-gb" lang="en-gb">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-language" content="en-gb" />
<meta http-equiv="imagetoolbar" content="no" />
<title><?php $page_title ?></title>
<link href="../adm/style/admin.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="ltr">
<div id="wrap">
	<div id="page-header">
		<h1><?php echo $page_title ?></h1>
		<p><a href="<?php echo $phpbb_root_path ?>"><?php echo $user->lang['INDEX'] ?></a></p>
		<p id="skip"><a href="#acp">Skip to content</a></p>
	</div>
	<div id="page-body">
		<div id="acp">
		<div class="panel">
			<span class="corners-top"><span></span></span>
				<div id="content">
					<div id="menu">
						<ul>
							<li <?php echo (($mode == 'else') ? $activemenu : '') ?>><a href="install.php"><span><?php echo $user->lang['INSTALLER_INTRO'] ?></span></a></li>
							<li class="header"><?php echo $user->lang['INSTALLER_INSTALL_MENU'] ?></li>
							<li <?php echo (($mode == 'install') ? $activemenu : '') ?>><a href="install.php?mode=install"><span><?php echo sprintf($user->lang['INSTALLER_INSTALL_VERSION'], $new_mod_version) ?></span></a></li>
							<li class="header"><?php echo $user->lang['INSTALLER_UPDATE_MENU'] ?></li>
							<li <?php echo (($mode == 'update100d') ? $activemenu : '') ?>><a href="install.php?mode=update100d&amp;v=1.0.0d"><span><?php echo $user->lang['INSTALLER_UPDATE_VERSION'] ?>1.0.0d</span></a></li>
							<li <?php echo (($mode == 'update011') ? $activemenu : '') ?>><a href="install.php?mode=update011&amp;v=0.1.1"><span><?php echo $user->lang['INSTALLER_UPDATE_VERSION'] ?>0.1.1</span></a></li>
						</ul>
					</div>
					<div id="main">
					<a name="maincontent"></a>
<?php
if ($mode == 'install')
{
	if ($install == 1)
	{
		if ($installed)
		{
			?>
			<div class="successbox">
				<h3><?php echo $user->lang['INFORMATION'] ?></h3>
				<p><?php echo sprintf($user->lang['INSTALLER_INSTALL_SUCCESSFUL'], $new_mod_version) ?></p>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="errorbox">
				<h3><?php echo $user->lang['WARNING'] ?></h3>
				<p><?php echo sprintf($user->lang['INSTALLER_INSTALL_UNSUCCESSFUL'], $new_mod_version) ?></p>
			</div>
			<?php 
		}
	}
	else
	{
		?>
		<h1><?php echo $user->lang['INSTALLER_INSTALL_WELCOME'] ?></h1>
		<p><?php echo $user->lang['INSTALLER_INSTALL_WELCOME_NOTE'] ?></p>
		<form id="acp_board" method="post" action="install.php?mode=install">
			<fieldset>
				<legend><?php echo $user->lang['INSTALLER_INSTALL'] ?></legend>
				<dl>
					<dt><label for="install">v<?php echo $new_mod_version ?>:</label></dt>
					<dd><label><input name="install" value="1" class="radio" type="radio" /><?php echo $user->lang['YES'] ?></label><label><input name="install" value="0" checked="checked" class="radio" type="radio" /><?php echo $user->lang['NO'] ?></label></dd>
				</dl>
				<p class="submit-buttons">
					<input class="button1" id="submit" name="submit" value="Submit" type="submit" />&nbsp;
					<input class="button2" id="reset" name="reset" value="Reset" type="reset" />
				</p>
			</fieldset>
		</form>
		<?php
	}
}
else if (($mode == 'update011') || ($mode == 'update100d'))
{
	if ($update == 1)
	{
		if ($updated)
		{
			?>
			<div class="successbox">
				<h3><?php echo $user->lang['INFORMATION'] ?></h3>
				<p><?php echo sprintf($user->lang['INSTALLER_UPDATE_SUCCESSFUL'], $version, $new_mod_version) ?></p>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="errorbox">
				<h3><?php echo $user->lang['WARNING'] ?></h3>
				<p><?php echo sprintf($user->lang['INSTALLER_UPDATE_UNSUCCESSFUL'], $version, $new_mod_version) ?></p>
			</div>
			<?php 
		}
	}
	else
	{
		?>
		<h1><?php echo $user->lang['INSTALLER_UPDATE_WELCOME'] ?></h1>
		<form id="acp_board" method="post" action="install.php?mode=<?php echo $mode ?>&amp;v=<?php echo $version ?>">
			<fieldset>
				<legend><?php echo $user->lang['INSTALLER_UPDATE'] ?></legend>
				<dl>
					<dt><label for="update"><?php echo sprintf($user->lang['INSTALLER_UPDATE_NOTE'], $version, $new_mod_version) ?>:</label></dt>
					<dd><label><input name="update" value="1" class="radio" type="radio" /><?php echo $user->lang['YES'] ?></label><label><input name="update" value="0" checked="checked" class="radio" type="radio" /><?php echo $user->lang['NO'] ?></label></dd>
				</dl>
				<p class="submit-buttons">
					<input class="button1" id="submit" name="submit" value="Submit" type="submit" />&nbsp;
					<input class="button2" id="reset" name="reset" value="Reset" type="reset" />
				</p>
			</fieldset>
		</form>
		<?php
	}
}
else if ($mode == 'else')
{
	?>
	<h1><?php echo $user->lang['INSTALLER_INTRO_WELCOME'] ?></h1>
	<p><?php echo $user->lang['INSTALLER_INTRO_WELCOME_NOTE'] ?></p>
	<?php
}
else
{
	?>
	<div class="errorbox">
		<h3>ERROR</h3>
		<p><?php echo $user->lang['INSTALLER_NEEDS_FOUNDER'] ?></p>
	</div>
	<?php
}
?>
						</div>
					</div>
				<span class="corners-bottom"><span></span></span>
			</div>
		</div>
	</div>
	<!--
		We request you retain the full copyright notice below including the link to www.phpbb.com.
		This not only gives respect to the large amount of time given freely by the developers
		but also helps build interest, traffic and use of phpBB. If you (honestly) cannot retain
		the full copyright we ask you at least leave in place the "Powered by phpBB" line, with
		"phpBB" linked to www.phpbb.com. If you refuse to include even this then support on our
		forums may be affected.
		The phpBB Group : 2006
	// -->
<div id="page-footer">Powered by phpBB &copy; 2000, 2002, 2005, 2007 <a href="http://www.phpbb.com/">phpBB Group</a><br />Installer by <a href="http://www.flying-bits.org/">nickvergessen</a></div>
</div>
</body>
</html>