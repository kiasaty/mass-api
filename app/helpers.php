<?php

/**
 * Get the title of the weekday.
 * 
 * @param  int $dayNumber
 * @return string 
 */
function getDayTitle($dayNumber)
{
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];

    return $days[$dayNumber];
}