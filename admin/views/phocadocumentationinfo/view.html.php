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

class  PhocaDocumentationCpViewPhocaDocumentationInfo extends JViewLegacy
{
	protected $t;

	function display($tpl = null) {
		$this->t	= PhocaDocumentationUtils::setVars();
		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$class	= $this->t['n'] . 'Utils';
		$this->t['version'] = $class::getExtensionVersion();
		$this->addToolbar();
		parent::display($tpl);
	}



	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t);

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocadocumentation" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCADOCUMENTATION_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCADOCUMENTATION_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		JToolbarHelper::title( JText::_($this->t['l'].'_PDC_INFO' ), 'info.png' );
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_'.$this->t['c']);
		}
		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
