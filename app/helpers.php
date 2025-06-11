<?php

if (!function_exists('isLinkActive')) {
    function isLinkActive($activeRoutes) {
        if (empty($activeRoutes)) return false;
        foreach ($activeRoutes as $route) {
            if (request()->routeIs($route)) return true;
        }
        return false;
    }
}
