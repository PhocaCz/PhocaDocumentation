<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
jimport('joomla.application.component.controller');

$l['cp']	= 'COM_PHOCADOCUMENTATION_CONTROL_PANEL';
$l['in']	= 'COM_PHOCADOCUMENTATION_INFO';
$l['c']		= 'COM_PHOCADOCUMENTATION_CATEGORIES';
$view	= JFactory::getApplication()->input->get('view');

if ($view == '' || $view == 'phocadocumentationcp') {
	JHtmlSidebar::addEntry(JText::_($l['cp']), 'index.php?option=com_phocadocumentation',true);
	JHtmlSidebar::addEntry(JText::_($l['c']), 'index.php?option=com_categories&extension=com_content');
	JHtmlSidebar::addEntry(JText::_($l['in']), 'index.php?option=com_phocadocumentation&view=phocadocumentationinfo' );
}

if ($view == 'phocadocumentationinfo') {
	JHtmlSidebar::addEntry(JText::_($l['cp']), 'index.php?option=com_phocadocumentation');
	JHtmlSidebar::addEntry(JText::_($l['c']), 'index.php?option=com_categories&extension=com_content');
	JHtmlSidebar::addEntry(JText::_($l['in']), 'index.php?option=com_phocadocumentation&view=phocadocumentationinfo', true );
} 

if ($view == 'categories') {
	JHtmlSidebar::addEntry(JText::_($l['cp']), 'index.php?option=com_phocadocumentation');
	JHtmlSidebar::addEntry(JText::_($l['c']), 'index.php?option=com_categories&extension=com_content', true);
	JHtmlSidebar::addEntry(JText::_($l['in']), 'index.php?option=com_phocadocumentation&view=phocadocumentationinfo' );
} 
http://localhost/J311/administrator/index.php?option=com_categories&extension=com_content
class PhocaDocumentationCpController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = array()){
		parent::display($cachable = false, $urlparams = array());
	}
}
?>
