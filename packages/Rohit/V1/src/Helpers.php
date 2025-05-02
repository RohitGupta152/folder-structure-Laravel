<?php

if (!function_exists('welcome_message')) {
    function welcome_message($name = 'Guest')
    {
        return "🎉 Welcome to our package, {$name}!";
    }
}
