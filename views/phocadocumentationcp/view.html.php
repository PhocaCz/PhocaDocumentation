<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );

class PhocaDocumentationCpViewPhocaDocumentationcp extends JViewLegacy
{
	protected $t;
	
	function display($tpl = null) {
	
		$this->t	= PhocaDocumentationUtils::setVars();
		$this->views= array(
		'categories'	=> $this->t['l'] . '_CATEGORIES',
		'info'			=> $this->t['l'] . '_INFO'
		);
		
		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$this->t['version'] = PhocaDocumentationHelper::getPhocaVersion('com_phocadocumentation');

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocadocumentationcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaDocumentationCpHelper::getActions($this->t);
		JToolBarHelper::title( JText::_( 'COM_PHOCADOCUMENTATION_PDC_CONTROL_PANEL' ), 'home-2' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolBar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocadocumentation" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCADOCUMENTATION_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCADOCUMENTATION_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_phocadocumentation');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help( 'screen.phocadocumentation', true );
	}
}
?>