<?php
if (!function_exists('roleCan')) {
    function roleCan(string $role, string $permission): bool
    {
        $permissions = config("roles_permissions.$role", []);
        return in_array($permission, $permissions) || $role === "owner";
    }
}