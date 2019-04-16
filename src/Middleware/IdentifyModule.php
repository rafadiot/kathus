<?php

namespace Kathus\Middleware;

use Kathus\RepositoryManager;
use Closure;

class IdentifyModule
{
    /**
     * @var RepositoryManager
     */
    protected $module;

    /**
     * IdentifyModule constructor.
     *
     * @param RepositoryManager $module
     */
    public function __construct(RepositoryManager $module)
    {
        $this->module = $module;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $slug = null)
    {
        $request->session()->flash('kathus', $this->module->where('slug', $slug));

        return $next($request);
    }
}
