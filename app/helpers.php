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

/**
 * Get the number of the weekday.
 * 
 * @param  string $dayTitle
 * @return int 
 */
function getDayNumber($dayTitle)
{
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];

    return array_search($dayTitle, $days);
}