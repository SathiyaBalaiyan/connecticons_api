<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {

        // Get all input data
        $input = $request->all();

        // Sanitize input data
        $sanitizedInput = array_map(function ($data) {

            $data = strip_tags($data);
            $data = htmlspecialchars($data);
            $data = stripslashes($data);
            $data = trim($data);
            return $data;
        }, $input);

        // Merge sanitized input back into the request
        $request->merge($sanitizedInput);

        // Proceed to the next middleware or the request handler
        return $next($request);
    }


}
