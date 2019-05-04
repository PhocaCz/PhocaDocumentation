<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @form Phoca form
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
class PhocaDocumentationNavigation
{
	private static $doc = false;

	private function __construct(){}

	public static function getDocuments() {

		if(self::$doc === false) {

			$app		= JFactory::getApplication();
			$id			= $app->input->get('id', 0, 'int');
			$user 		= JFactory::getUser();
			$userLevels	= implode (',', $user->getAuthorisedViewLevels());
			$db 		= JFactory::getDBO();

            $pC	 					= JComponentHelper::getParams('com_phocadocumentation');
            $ordering				= $pC->get( 'article_ordering', 1 );
            $articleOrdering 		= PhocaDocumentationHelperFront::getOrderingText($ordering);

			// CURRENT DOC (Information about current doc - ordering, category)
			$wheres		= array();
			$wheres[]	= " c.id= ".(int)$id;
			$wheres[]	= " c.catid= cc.id";

			$wheres[] = ' c.state = 1';
			$wheres[] = ' cc.published = 1';
			$wheres[] = " cc.access IN (".$userLevels.")";

			// Active
			$jnow		= JFactory::getDate();
			$now		= $jnow->toSQL();
			$nullDate	= $db->getNullDate();
			$wheres[] = ' ( c.publish_up = '.$db->Quote($nullDate).' OR c.publish_up <= '.$db->Quote($now).' )';
			$wheres[] = ' ( c.publish_down = '.$db->Quote($nullDate).' OR c.publish_down >= '.$db->Quote($now).' )';


			$query = ' SELECT c.id, c.title, c.alias, c.catid, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias, cc.access as cataccess,'
					.' MIN(n.id) AS nextid, MIN(n.title) AS nexttitle, MIN(n.alias) AS nextalias,'
					.' MAX(p.id) AS previd, MAX(p.title) AS prevtitle, MAX(p.alias) AS prevalias'
					.' FROM #__content AS c'
					.' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
					.' LEFT JOIN #__content AS n ON cc.id = n.catid AND n.ordering > c.ordering'
					.' LEFT JOIN #__content AS p ON cc.id = p.catid AND p.ordering < c.ordering'
					. ' WHERE ' . implode( ' AND ', $wheres )
                    . ' GROUP BY c.id, c.title, c.alias, c.catid'
                    . ' ORDER BY c.'.$articleOrdering;


			$db->setQuery($query, 0, 1);

			$cDoc = $db->loadObject();

			if (!empty($cDoc)) {

				$d['doc']['id']		= (int)$cDoc->id;
				$d['doc']['title']	= $cDoc->title;
				$d['doc']['alias']	= $cDoc->alias;
				$d['doc']['cid']	= (int)$cDoc->catid;
				$d['doc']['calias']	= $cDoc->categoryalias;

				if (isset($cDoc->nextid) && (int)$cDoc->nextid > 0) {
					$d['next']['id']	= (int)$cDoc->nextid;
					$d['next']['title']	= $cDoc->nexttitle;
					$d['next']['alias']	= $cDoc->nextalias;
					$d['next']['cid']	= (int)$cDoc->catid;
					$d['next']['calias']= $cDoc->categoryalias;
				} else {
					$d['next'] = array();
				}
				if (isset($cDoc->previd) && (int)$cDoc->previd > 0) {
					$d['prev']['id']	= (int)$cDoc->previd;
					$d['prev']['title']	= $cDoc->prevtitle;
					$d['prev']['alias']	= $cDoc->prevalias;
					$d['prev']['cid']	= (int)$cDoc->catid;
					$d['prev']['calias']= $cDoc->categoryalias;
				} else {
					$d['prev'] = array();
				}

				// Query LIST
				$wheres		= array();
				$wheres[]	= " c.catid= ".(int)$cDoc->catid;
				$wheres[]	= " c.catid= cc.id";
				$wheres[] 	= " cc.access IN (".$userLevels.")";
				$wheres[] 	= " c.state = 1";
				$wheres[] 	= " cc.published = 1";
				$query = " SELECT c.id, c.title, c.alias, cc.id AS cid, cc.title AS ctitle, cc.alias AS calias"
				." FROM #__content AS c, #__categories AS cc"
				." WHERE " . implode( " AND ", $wheres )
                .' ORDER BY c.'.$articleOrdering;
				$db->setQuery($query);
				$lDoc = $db->loadAssocList();
				if (!empty($lDoc)) {
					$d['list'] = $lDoc;
				} else {
					$d['list'] = array();
				}


			} else {
				$d['next']	= array();
				$d['prev']	= array();
				$d['list']	= array();
				$d['doc']	= array();
			}

		
			self::$doc = $d;
		}
		return self::$doc;
	}



	public final function __clone() {
		throw new Exception('Function Error: Cannot clone instance of Singleton pattern', 500);
		return false;
	}



	public static function getPrevOutput($d, $oL, $itemId, $bts = 0) {
		$t		= JText::_('PLG_CONTENT_PHOCADOCUMENTATIONNAVIGATION_PREVIOUS');
		if (!empty($d)) {

			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/prev.png', $t);

			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-left ph-icon-arrow"></span>';}

			$link = PhocaDocumentationHelperRoute::getArticleRoute($d['id'], $d['alias'], $d['cid'], $d['calias'], $itemId);

			$o =  '<a '.self::getOverlib($d['title'], $t, $oL).' href="'. JRoute::_($link).'">'.$img.'</a>';
		} else {
			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/prev-grey.png', $t);
			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-left ph-icon-not-active"></span>';}
			$o = $img;
		}
		return $o;
	}

	public static function getNextOutput($d, $oL, $itemId, $bts = 0) {
		$t		= JText::_('PLG_CONTENT_PHOCADOCUMENTATIONNAVIGATION_NEXT');
		if (!empty($d)) {

			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/next.png', $t);

			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-right ph-icon-arrow"></span>';}

			$link = PhocaDocumentationHelperRoute::getArticleRoute($d['id'], $d['alias'], $d['cid'], $d['calias'], $itemId);

			$o =  '<a '.self::getOverlib($d['title'], $t, $oL).' href="'. JRoute::_($link).'">'.$img.'</a>';
		} else {
			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/next-grey.png', $t);
			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-right ph-icon-not-active"></span>';}
			$o = $img;
		}
		return $o;
	}

	public static function getTopOutput($d, $oL, $topId, $itemId, $bts = 0) {
		$t		= JText::_('PLG_CONTENT_PHOCADOCUMENTATIONNAVIGATION_TOP');
		if (!empty($d)) {

			if ($topId == '') {
				$topId = 'pdoc-top';// go to main navigation instead of top site
			}


			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/up.png', $t);

			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-up ph-icon-arrow"></span>';}

			$link = PhocaDocumentationHelperRoute::getArticleRoute($d['id'], $d['alias'], $d['cid'], $d['calias'], $itemId) .'#'.$topId;

			$o =  '<a '.self::getOverlib($d['title'], $t, $oL).' href="'. JRoute::_($link).'">'.$img.'</a>';
		} else {
			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/up-grey.png', $t);
			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-chevron-up ph-icon-not-active"></span>';}
			$o = $img;
		}
		return $o;
	}

	public static function getListOutput($d, $dd, $oL, $itemId, $bts = 0) {

		$o = '';
		$oBox = '';
		if (!empty($d)) {
			$oBox .= '<div style="text-align:left" id="phoca-doc-category-box-plugin">';
			$imgArticle = '';
			if ($bts == 1) {$imgArticle = '<span class="glyphicon glyphicon-book ph-icon-arrow"></span> ';}

			foreach ($d as $k => $v) {
				$link = PhocaDocumentationHelperRoute::getArticleRoute($v['id'], $v['alias'], $v['cid'], $v['calias'], $itemId);
				$oBox .= '<div class="pdoc-document ph-plugin-document">'.$imgArticle.'<a title="'.$v['title'].'" href="'. JRoute::_($link).'">'.$v['title'].'</a></div>';
			}
			$oBox .= '</div>';

			$oBox = htmlspecialchars( addslashes('<div class="pdoc-overlib ph-overlib">'.$oBox.'</div>') );
		}

		if (!empty($dd)) {

			$t		= JText::_('PLG_CONTENT_PHOCADOCUMENTATIONNAVIGATION_TABLE_OF_CONTENTS');
			$img	= JHTML::_('image', 'media/com_phocadocumentation/images/icon-category.png', $t);

			if ($bts == 1) {$img = '<span class="glyphicon glyphicon-folder-close ph-icon-folder"></span>';}

			$o = '<a '.self::getOverlib($oBox, $t, $oL, 'STICKY, MOUSEOFF,').' href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($dd['cid'], $dd['calias'])).'">'.$img.'</a>';

		}
		return $o;
	}

	public static function getOverLib($title, $text, $oL, $addParams = '') {
		return " onmouseover=\"return overlib('".$title."', CAPTION, '".$text."', BELOW, RIGHT, BGCLASS, 'bgPhocaPDocClass', CLOSECOLOR, '".$oL['closecolor']."', FGCOLOR, '".$oL['fgcolor']."', BGCOLOR, '".$oL['bgcolor']."', TEXTCOLOR, '".$oL['textcolor']."', CAPCOLOR, '".$oL['capcolor']."', ".$addParams." TEXTFONT, 'sans-serif, arial', TEXTSIZE, '2', 'CAPTIONFONT', 'san-serif, arial', CLOSEFONT, 'sans-serif, arial');\""
		. " onmouseout=\"return nd();\"";

	}



	/* Possible Hardcode */

	public static function renderTop($p, $n, $l, $h) {

		$sep = ' <b style="color:#ccc;">&bull;</b> ';
		$o = '';
		$o .= '<div class="navigation-text" id="pdoc-top"><h5>'.$h . '</h5>'."\n";
		$o .= $p . $sep . $l . $sep. $n;
		$o .= '</div>';
		return $o;
	}

	public static function renderBottom($p, $n, $t) {

		$sep = ' <b style="color:#ccc;">&bull;</b> ';
		$o = '';
		$o .= '<div class="navigation-text" id="pdoc-top">'."\n";
		$o .= $p . $sep . $t . $sep. $n;
		$o .= '</div>';
		return $o;
	}


}
?>
