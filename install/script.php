<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport( 'joomla.filesystem.folder' );

class com_phocadocumentationInstallerScript
{
	function install($parent) {
		JFactory::getApplication()->enqueueMessage($message, 'message');
		$parent->getParent()->setRedirectURL('index.php?option=com_phocadocumentation');
	}
	function uninstall($parent) {
		//echo '<p>' . JText::_('COM_PHOCADOCUMENTATION_UNINSTALL_TEXT') . '</p>';
	}

	function update($parent) {
		//echo '<p>' . JText::sprintf('COM_PHOCADOCUMENTATION_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
		$msg =  JText::_('COM_PHOCADOCUMENTATION_UPDATE_TEXT');
		$msg .= ' (' . JText::_('COM_PHOCADOCUMENTATION_VERSION'). ': ' . $parent->get('manifest')->version . ')';
		$msg .= '<br />'. $message;
		$app		= JFactory::getApplication();
		$app->enqueueMessage($msg, 'message');
		$app->redirect(JRoute::_('index.php?option=com_phocadocumentation'));
	}

	function preflight($type, $parent) {
		//echo '<p>' . JText::_('COM_PHOCADOCUMENTATION_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	function postflight($type, $parent)  {
		//echo '<p>' . JText::_('COM_PHOCADOCUMENTATION_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
?>