<?php


/**
 * Fonction échappant les caractères html dans $message
 * @param string $message chaîne à échapper
 * @return string chaîne échappée
 */
function e($message)
{
    return htmlspecialchars($message, ENT_QUOTES);
}

function checkUserAccess()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_token']) || $_SESSION['expire_time'] < time()) {
        return false;
    }

    $user = Model::getModel()->verifierToken(e($_SESSION['user_token']));

    if (!$user) {
        return false;
    }

    return $user;
}
