<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');


class PhocaDocumentationHelper
{
	/**
	 * Method to get Phoca Version
	 * @return string Version of Phoca Gallery
	 */
	public static function getPhocaVersion()
	{
		$folder = JPATH_ADMINISTRATOR . '/components/com_phocadocumentation';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE . '/components/com_phocadocumentation';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (!empty($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = \JInstaller::parseXMLInstallFile($folder.'/'.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}

		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}

    public static function getInfo() {

        JPluginHelper::importPlugin('phocatools');
        $results = \JFactory::getApplication()->triggerEvent('PhocatoolsOnDisplayInfo', array('NjI5NTcyNzcxNw=='));
        if (isset($results[0]) && $results[0] === true) {
            return '';
        }

        return '<p>&nbsp;</p><div style="text-align:right">Powered by <a href="https://www.phoca.cz/phocadocumentation">Phoca Documentation</a></div>';

    }

    /*
	public static function getPhocaId($id){
		$v	= PhocaDocumentationHelper::getPhocaVersion();
		$i	= str_replace('.', '',substr($v, 0, 3));
		$n	= '<p>&nbsp;</p>';
		$l	= 'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'d'.'o'.'c'.'u'.'m'.'e'.'n'.'t'.'a'.'t'.'i'.'o'.'n';
		$t	= 'P'.'o'.'w'.'e'.'r'.'e'.'d'.' '.'b'.'y';
		$p	= 'P'.'h'.'o'.'c'.'a'.' '.'D'.'o'.'c'.'u'.'m'.'e'.'n'.'t'.'a'.'t'.'i'.'o'.'n';
		$s	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'d'.'e'.'c'.'o'.'r'.'a'.'t'.'i'.'o'.'n'.':'.'n'.'o'.'n'.'e'.'"';
		$s2	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'a'.'l'.'i'.'g'.'n'.':'.'c'.'e'.'n'.'t'.'e'.'r'.';'.'c'.'o'.'l'.'o'.'r'.':'.'#'.'d'.'3'.'d'.'3'.'d'.'3'.'"';
		$b	= 't'.'a'.'r'.'g'.'e'.'t'.'='.'"'.'_'.'b'.'l'.'a'.'n'.'k'.'"';
		$i	= (int)$i * 3;
		$output	= '';
		if ($id != $i) {
			$output		.= $n;
			$output		.= '<div '.$s2.'>';
		}

		if ($id == $i) {
			$output	.= '<!-- <a href="'.$l.'">site: www.phoca.cz | version: '.$v.'</a> -->';
		} else {
			$output	.= $t . ' <a href="'.$l.'" '.$s.' '.$b.' title="'.$p.'">'. $p. '</a>';
		}
		if ($id != $i) {
			$output		.= '</div>' . $n;
		}
		return $output;
	}*/

	public static function displayNewIcon ($date, $time = 0, $icon = 1) {

		if ($time == 0) {
			return '';
		}

		$dateAdded 	= strtotime($date, time());
		$dateToday 	= time();
		$dateExists = $dateToday - $dateAdded;
		$dateNew	= $time * 24 * 60 * 60;

		if ($dateExists < $dateNew) {
			if ($icon == 1) {
				return '&nbsp;'. JHTML::_('image', 'media/com_phocadocumentation/images/icon-new.png', JText::_('COM_PHOCADOCUMENTATION_NEW'));
			} else {
				return '&nbsp;<span class="label label-warning">'.JText::_('COM_PHOCADOCUMENTATION_NEW').'</span>';
			}
		} else {
			return '';
		}

	}

	public static function displayHotIcon ($hits, $requiredHits = 0, $icon = 1) {

		if ($requiredHits == 0) {
			return '';
		}

		if ($requiredHits <= $hits) {
			if ($icon == 1) {
				return '&nbsp;'. JHTML::_('image', 'media/com_phocadocumentation/images/icon-hot.png',JText::_('COM_PHOCADOCUMENTATION_HOT'));
			} else {
				return '&nbsp;<span class="label label-important">'.JText::_('COM_PHOCADOCUMENTATION_HOT').'</span>';
			}
		} else {
			return '';
		}

	}

}
?>
