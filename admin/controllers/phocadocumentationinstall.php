<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();

class PhocaDocumentationCpControllerPhocaDocumentationinstall extends PhocaDocumentationCpController
{
	function __construct() {
		parent::__construct();	
	}

	function install() {		
		$msg = JText::_( 'Phoca Documentation successfully installed' );
		
		$link = 'index.php?option=com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
	
	function upgrade() {
		$msg = JText::_( 'Phoca Documentation successfully upgraded' );
		
		$link = 'index.php?option=com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
}