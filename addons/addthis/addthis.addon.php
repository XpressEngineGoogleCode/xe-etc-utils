<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file addthis.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief AddThis 애드온
     **/

    if($called_position == 'before_display_content') {
        if (Context::getResponseMethod() == 'HTML') {
            switch ($addon_info->type) {
                case 1:
                    $img = '<img src="//s7.addthis.com/static/btn/v2/lg-bookmark-en.gif" width="125" height="16" border="0" alt="Bookmark" style="vertical-align: middle" />';
                    break;
                case 2:
                    $img = '<img src="//s7.addthis.com/static/btn/sm-share-en.gif" width="83" height="16" border="0" alt="Share" style="vertical-align: middle"/>';
                    break;
                case 3:
                    $img = '<img src="//s7.addthis.com/static/btn/sm-bookmark-en.gif" width="83" height="16" border="0" alt="Bookmark" style="vertical-align: middle"/>';
                    break;
                default:
                    $img = '<img src="//s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" border="0" alt="Share" style="vertical-align: middle"/>';
            }

            $addThis = sprintf('<div style="text-align: right;"><a class="addthis_button" href="http://www.addthis.com/bookmark.php">%s</a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script></div>', $img);
            
//            $output = preg_replace($pattern, "$0$addThis", $output);
            $output = str_replace('<!--AfterDocument(', "$addThis<!--AfterDocument(", $output);
        }
    }
?>
