<?php
use App\Models\RegisteredUser;

//PAGE NAME
if (!function_exists("page_name")) {
    function page_name($name)
    {
        $path = explode('/', Request::path());
        $menu_name = strtolower($path[0]);
        $main = str_replace("_", " ", $menu_name);
        $sub  = isset($path[1]) && $path[1] ? strtolower($path[1]) : '';

        $menu = [
            'menu' => $menu_name,
            'main' => $main,
            'sub' => $sub,
            'page_title' => $main
        ];

        return $menu[$name];
    }
}

// CURRENT PAGE
if (!function_exists("current_page")) {
    function current_page()
    {
        $link  = Request::segments();
        return end($link);
    }
}

if(!function_exists(("user_name"))){
    function user_name($id){
        $user = RegisteredUser::find($id);
   
        $first_name = $user->first_name ?? '-';
        $last_name = $user->last_name ?? '-';

        return $first_name . ' ' . $last_name;
    }
}

// GENERIC AUDIT LOG
if (!function_exists('log_audit')) {
    /**
     * Save an audit log entry.
     *
     * @param string $module
     * @param string $action
     * @param int|null $recordId
     * @param mixed $previous
     * @param mixed $new
     * @param string|null $submodule
     * @param string|null $empNumber  (optional override)
     */
    function log_audit(
        string $module,
        string $action,
        $recordId = null,
        $previous = null,
        $new = null,
        $submodule = null,
        $empNumber = null
    ) {
        // submodule is an optional string you can use to further classify the
        // log entry.  For example you might pass the name of the modal that was
        // open, the controller method, a feature name, etc.  It does *not* have
        // to correspond to the JavaScript modal element; it's just metadata for
        // your own organization.

        $data = [
            // emp_number resolution order:
            // 1. explicit override passed to helper
            // 2. value stored in session under 'emp_number' (if your login flow sets this)
            // 3. authenticated user's user_name field (holds employee code in this app)
            // 4. fallback to user id as a last resort
            'emp_number' => $empNumber
                ?? session('emp_number')
                ?? Auth::id(),
            'record_id' => $recordId,
            'module' => $module,
            'sub_module' => $submodule,
            'action' => $action,
            'previous_data' => is_array($previous) || is_object($previous) ? json_encode($previous) : $previous,
            'new_data' => is_array($new) || is_object($new) ? json_encode($new) : $new,
            'ip_address' => request()->ip(),
        ];

        \App\Models\AuditLog::create($data);
    }
}
