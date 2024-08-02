<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Support\Facades\Gate;

class Authorize
{
    public function handle($request, $next)
    {
        if (! Gate::check('viewMailcoach', [$request->user(config('mailcoach.guard'))])) {
            if ($redirectRoute = config('mailcoach.redirect_unauthorized_users_to_route')) {
                return redirect()->route($redirectRoute);
            }

            abort(403);
        }

        return $next($request);
    }
}
