<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 视图
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_View extends Typecho_Widget
{
    /**
     * 调用的风格文件
     *
     * @access private
     * @var string
     */
    private $_themeFile;
	/**
     * 风格目录
     * 
     * @access private
     * @var string
     */
    private $_themeDir;
}
