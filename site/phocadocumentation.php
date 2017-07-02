<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocadocumentation.php' );
require_once( JPATH_COMPONENT.'/helpers/route.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocadocumentation/helpers/phocadocumentation.php' );



$controller = JControllerLegacy::getInstance('PhocaDocumentation');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>