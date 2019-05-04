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


			$query = ' SELECT c.id, c.title, c.alias, c.catid, c.ordering, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias, cc.access as cataccess'
				//.' n.id AS nextid, n.title AS nexttitle, n.alias AS nextalias,'
				//.' p.id AS previd, p.title AS prevtitle, p.alias AS prevalias'
				.' FROM #__content AS c'
				.' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
				//.' LEFT JOIN #__content AS n ON cc.id = n.catid AND n.ordering > c.ordering'
				//.' LEFT JOIN #__content AS p ON cc.id = p.catid AND p.ordering < c.ordering'
				. ' WHERE ' . implode( ' AND ', $wheres )
				//. ' GROUP BY c.id, c.title, c.alias, c.catid, c.ordering, cc.id, cc.title, cc.alias, cc.access'
				. ' ORDER BY c.'.$articleOrdering;


			$db->setQuery($query, 0, 1);

			$cDoc = $db->loadObject();

			$d['next']	= array();
			$d['prev']	= array();
			$d['list']	= array();
			$d['doc']	= array();

			if (!empty($cDoc)) {

				$d['doc']['id']		= (int)$cDoc->id;
				$d['doc']['title']	= $cDoc->title;
				$d['doc']['alias']	= $cDoc->alias;
				$d['doc']['cid']	= (int)$cDoc->catid;
				$d['doc']['calias']	= $cDoc->categoryalias;

			/*	if (isset($cDoc->nextid) && (int)$cDoc->nextid > 0) {
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
				} */

				// Query LIST
				$wheres		= array();
				$wheres[]	= " c.catid= ".(int)$cDoc->catid;
				$wheres[]	= " c.catid= cc.id";
				$wheres[] 	= " cc.access IN (".$userLevels.")";
				$wheres[] 	= " c.state = 1";
				$wheres[] 	= " cc.published = 1";
				$query = " SELECT c.id, c.title, c.alias, c.ordering, cc.id AS cid, cc.title AS ctitle, cc.alias AS calias"
				." FROM #__content AS c, #__categories AS cc"
				." WHERE " . implode( " AND ", $wheres )
                .' ORDER BY c.'.$articleOrdering;
				$db->setQuery($query);
				$lDoc = $db->loadAssocList();

				$currentArrayId = 0;
				if (!empty($lDoc)) {
					$d['list'] = $lDoc;
					foreach($lDoc as $k => $v) {

						if (isset($v['id']) && (int)$v['id'] == (int)$id) {
							$currentArrayId = $k;
							break;
						}
					}

					// We don't search for ordering or id but for array key
					// the array key starts from 0 ++ and it is not ordering or id so we can get prev and next
					// in case the key will be ordering, there can be missing the number: 1 2 4 6 so then nothing will be found
					$next = $currentArrayId + 1;
					$prev = $currentArrayId - 1;// It can be even minus, there will be check for this

					if (isset($lDoc[$next]) && !empty($lDoc[$next]) && isset($lDoc[$next]['id'])) {
						$d['next']['id']	= (int)$lDoc[$next]['id'];
						$d['next']['title']	= $lDoc[$next]['title'];
						$d['next']['alias']	= $lDoc[$next]['alias'];
						$d['next']['cid']	= (int)$lDoc[$next]['cid'];
						$d['next']['ctitle']= $lDoc[$next]['ctitle'];
						$d['next']['calias']= $lDoc[$next]['calias'];
					}
					if (isset($lDoc[$prev]) && !empty($lDoc[$prev]) && isset($lDoc[$prev]['id'])) {
						$d['prev']['id']	= (int)$lDoc[$prev]['id'];
						$d['prev']['title']	= $lDoc[$prev]['title'];
						$d['prev']['alias']	= $lDoc[$prev]['alias'];
						$d['prev']['cid']	= (int)$lDoc[$prev]['cid'];
						$d['prev']['ctitle']= $lDoc[$prev]['ctitle'];
						$d['prev']['calias']= $lDoc[$prev]['calias'];
					}
				}

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
