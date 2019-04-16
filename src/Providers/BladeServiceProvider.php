<?php

namespace Kathus\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('module', function ($slug) {
                return "<?php if (Kathus::exists({$slug}) && Kathus::isEnabled({$slug})): ?>";
            });

            $bladeCompiler->directive('endmodule', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
