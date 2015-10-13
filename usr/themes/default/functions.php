<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form) {
    
}


/**
 * 重写评论显示函数
 */
function threadedComments($comments, $singleCommentOptions){
    $comments->realAuthorUrl = $comments->authorId ?  $comments->poster->ucenter : 'javascript:;';
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass = ' reply-by-author';
        }
    }

    echo "<div id=\"{$comments->theId}\" class=\"cell{$commentClass}\"><div class=\"reply-avatar fl\">";
    //$comments->gravatar($singleCommentOptions->avatarSize, $singleCommentOptions->defaultAvatar);
    echo '<img class="avatar" src="'.$comments->poster->avatar.'" width="48">';
    echo '</div><div class="fr">';
    //$comments->reply($singleCommentOptions->replyWord);
    echo '<a href="javascript:replyAt(\''.$comments->poster->name.'\');">' . $singleCommentOptions->replyWord . '</a>';
    echo '</div><p><a href="'.$comments->realAuthorUrl.'">'.$comments->poster->name.'</a>';
    echo '<span class="reply-time">';
    $comments->dateWord();
    echo '</span></p><div class="reply-content">';
    $comments->content();
    echo '</div></div>';
    if ($comments->children){
        //echo '<div class="reply-child reply-level-'.$comments->levels.'">';
        $comments->threadedComments();
        //echo '</div>';
    }
}

