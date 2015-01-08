<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: URIWidget.class.php 2014-12-17 下午03:56:18 pony_chiang $
 */

defined ( 'PDM_URL' ) or exit ( 'Access Denied' );

/**
 * URI 兼容
 * 模板调用示例
 * {:W('URI')}
 * 
 * @package Widget
 *         
 */
class URIWidget extends Widget {

	function render($data) {
		if (C ( 'URL_MODEL' ) == 0) {
			$_var_pathinfo = C ( 'VAR_PATHINFO' );
			// 该项目启用分组
			$html = '<input type="hidden" name="' . $_var_pathinfo . '" value="' . strtolower ( GROUP_NAME . '/' . MODULE_NAME . '/' . ACTION_NAME ) . '">';
			return $html;
		}
	}
}