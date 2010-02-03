<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file comment.addon.php
     * @author 난다날아 (sinsy200@gmail.com)
     * @brief 새 댓글에 new 표시를 합니다.
     *
     **/

    // HTML 출력 직전
    if($called_position != 'before_display_content' || Context::getResponseMethod() != 'HTML' ) return;

    // 새 댓글 표시 시간
    $time_interval = intVal($addon_info->duration_new) * 60 * 60;
    if (!$time_interval)    $time_interval = 24 * 60 * 60;

    $time_check = date("YmdHis", time()-$time_interval);


    // new 이미지
    if (!empty($addon_info->new_image)) {
        $new_image = sprintf('<img src="%s" alt="new" title="new" style="margin-left:2px;" class="addon_comment_new"/>', $addon_info->new_image);
    }else{
        $path = sprintf('%s%s',getUrl(), 'modules/document/tpl/icons/');
        $new_image = sprintf('<img src="%snew.gif" alt="new" title="new" style="margin-left:2px;" class="addon_comment_new"/>', $path);
    }

    // 현재 화면의 댓글 목록 구하기
    $pattern = "/<!--BeforeComment\((.*),.*\)-->/U";
    unset($matches);
    $res = preg_match_all($pattern, $output, $matches);

    if (!$res)    return;

    $oCommentModel = &getModel('comment');

    // 댓글 item 구하기
    $comment_srls = $matches[1];
    $oComments = $oCommentModel->getComments($comment_srls);

    // new 이미지 적용
    foreach($oComments as $oComment) {
        if ($oComment->regdate > $time_check) {
            if ($addon_info->position) {
                $pattern = sprintf('/<!--AfterComment\(%s,.*\)-->/U', $oComment->comment_srl);
                $output = preg_replace($pattern, "$new_image $0", $output);
            }else{
                $pattern = sprintf('/<!--BeforeComment\(%s,.*\)-->/U', $oComment->comment_srl);
                $output = preg_replace($pattern, "$0 $new_image", $output);
            }
        }
    }
?>