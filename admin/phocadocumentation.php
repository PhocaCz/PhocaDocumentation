<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocadocumentationcp.php' );
require_once( JPATH_COMPONENT.'/helpers/phocadocumentation.php' );
require_once( JPATH_COMPONENT.'/helpers/phocadocumentationutils.php' );
require_once( JPATH_COMPONENT.'/helpers/phocadocumentationrenderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaDocumentationCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
