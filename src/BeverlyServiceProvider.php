<?php

namespace Xbigdaddyx\Beverly;

use App\View\Components\Navbar;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xbigdaddyx\Beverly\Commands\BeverlyCommand;
use Xbigdaddyx\Beverly\Components\AppLayout;
use Xbigdaddyx\Beverly\Components\NavbarContent;
use Xbigdaddyx\Beverly\Filament\Pages\CartonDashboard;
use Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxPercentageTypeChart;
use Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxSummaryChart;
use Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxPoChart;
use Xbigdaddyx\Beverly\Livewire\SearchCarton;
use Xbigdaddyx\Beverly\Livewire\ValidationAttribute;
use Xbigdaddyx\Beverly\Livewire\ValidationCarton;
use Xbigdaddyx\Beverly\Livewire\ValidationStat;
use Xbigdaddyx\Beverly\Testing\TestsBeverly;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;


class BeverlyServiceProvider extends PackageServiceProvider
{
    public static string $name = 'beverly';

    public static string $viewNamespace = 'beverly';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package->name(static::$name)
            ->hasCommands($this->getCommands())

            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('xbigdaddyx/beverly');
            });

        $configFileName = $package->shortName();
        if (file_exists($package->basePath("/../routes/web.php"))) {
            $package->hasRoutes("web");
        }
        if (file_exists($package->basePath("/../routes/api.php"))) {
            $package->hasRoutes("api");
        }
        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->bind('BeverlySearchRepository', \Xbigdaddyx\Beverly\Repositories\SearchRepository::class);
        $this->app->bind('BeverlyVerificationRepository', \Xbigdaddyx\Beverly\Repositories\VerificationRepository::class);
    }

    public function packageBooted(): void
    {
        Notifications::alignment(Alignment::Center);
        Notifications::verticalAlignment(VerticalAlignment::Center);
        \Illuminate\Support\Facades\Blade::componentNamespace('Xbigdaddyx\\Beverly\\Components', 'beverly');
        $this->publishes([__DIR__ . '/../public/vendor/xbigdaddyx/beverly' => public_path('vendor/xbigdaddyx/beverly')], 'beverly-assets');
        if (class_exists(Livewire::class)) {
            Livewire::component('carton-box-summary-chart', CartonBoxSummaryChart::class);
            Livewire::component('carton-dashboard', CartonDashboard::class);
            Livewire::component('carton-box-percentage-type-chart', CartonBoxPercentageTypeChart::class);
            Livewire::component('carton-box-buyer-chart', CartonBoxPoChart::class);
            Livewire::component('search-carton', SearchCarton::class);
            Livewire::component('validation-carton', ValidationCarton::class);
            Livewire::component('validation-stat', ValidationStat::class);
            Livewire::component('validation-attribute', ValidationAttribute::class);
        }
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/beverly/{$file->getFilename()}"),
                ], 'beverly-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsBeverly());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'xbigdaddyx/beverly';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('beverly', __DIR__ . '/../resources/dist/components/beverly.js'),
            Css::make('/../public/vendor/xbigdaddyx/beverly', 'vendor/xbigdaddyx/beverly'),
            // Js::make('beverly-scripts', __DIR__ . '/../resources/dist/beverly.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            // BeverlyCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            // 'create_beverly_table',
        ];
    }
}
