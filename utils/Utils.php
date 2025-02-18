<?php

class Utils
{
    public static function sanitize_input($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}
?>