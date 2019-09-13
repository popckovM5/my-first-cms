<?php

/**
 * Выводит все что угодно
 * 
 * @param type $array
 */
function trace($array)
{
    echo '<div id="trace">';
    echo '<h3><pre>' . print_r($array , 1) . '</pre></h3>';
    echo '</div>';
}

?>
