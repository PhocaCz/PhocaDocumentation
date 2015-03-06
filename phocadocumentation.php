<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.filesystem.folder');
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocadocumentationcp.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocadocumentation.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocadocumentationutils.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'phocadocumentationrenderadmin.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminview.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'renderadminviews.php' );
jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaDocumentationCp');
$controller->execute(JFactory::getApplication()->input->get('task')); 
$controller->redirect();
?>