<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminConsoleController extends Controller
{
    public function index(): View
    {
        return view('role.admin.console', [
            'migrationPaths' => $this->migrationRelativePaths(),
            'seederClasses' => $this->discoverSeederClasses(),
        ]);
    }

    public function migrate(): RedirectResponse
    {
        $exitCode = Artisan::call('migrate', ['--force' => true]);

        return $this->respondWithArtisanOutput($exitCode);
    }

    public function migratePath(Request $request): RedirectResponse
    {
        $allowed = $this->migrationRelativePaths();

        $validated = $request->validate([
            'path' => ['required', 'string', Rule::in($allowed)],
        ]);

        $this->assertMigrationPathAllowed($validated['path']);

        $exitCode = Artisan::call('migrate', [
            '--path' => $validated['path'],
            '--force' => true,
        ]);

        return $this->respondWithArtisanOutput($exitCode);
    }

    public function seedAll(): RedirectResponse
    {
        $exitCode = Artisan::call('db:seed', ['--force' => true]);

        return $this->respondWithArtisanOutput($exitCode);
    }

    public function seedClass(Request $request): RedirectResponse
    {
        $allowed = $this->discoverSeederClasses();

        $validated = $request->validate([
            'class' => ['required', 'string', Rule::in($allowed)],
        ]);

        abort_unless(class_exists($validated['class']), 403);

        $exitCode = Artisan::call('db:seed', [
            '--class' => $validated['class'],
            '--force' => true,
        ]);

        return $this->respondWithArtisanOutput($exitCode);
    }

    public function tool(Request $request): RedirectResponse
    {
        $tools = [
            'migrate_status' => 'migrate:status',
            'optimize_clear' => 'optimize:clear',
            'cache_clear' => 'cache:clear',
            'config_clear' => 'config:clear',
            'route_clear' => 'route:clear',
            'view_clear' => 'view:clear',
            'about' => 'about',
        ];

        $validated = $request->validate([
            'tool' => ['required', 'string', Rule::in(array_keys($tools))],
        ]);

        $command = $tools[$validated['tool']];
        $exitCode = Artisan::call($command);

        return $this->respondWithArtisanOutput($exitCode);
    }

    /**
     * @return list<string>
     */
    private function migrationRelativePaths(): array
    {
        $dir = database_path('migrations');
        $files = glob($dir.DIRECTORY_SEPARATOR.'*.php') ?: [];
        sort($files);

        return array_values(array_map(
            fn (string $full): string => 'database/migrations/'.basename($full),
            $files,
        ));
    }

    /**
     * @return list<string>
     */
    private function discoverSeederClasses(): array
    {
        $path = database_path('seeders');
        $files = glob($path.DIRECTORY_SEPARATOR.'*.php') ?: [];
        $classes = [];

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $fqcn = 'Database\\Seeders\\'.$name;
            if (class_exists($fqcn)) {
                $classes[] = $fqcn;
            }
        }

        sort($classes);

        return array_values(array_unique($classes));
    }

    private function assertMigrationPathAllowed(string $path): void
    {
        $full = realpath(base_path($path));
        $base = realpath(database_path('migrations'));

        abort_unless(
            $full && $base && str_starts_with($full, $base) && is_file($full),
            403,
        );
    }

    private function respondWithArtisanOutput(int $exitCode): RedirectResponse
    {
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            return back()
                ->with('console_error', true)
                ->with(
                    'console_output',
                    $output !== '' ? $output : __('roleui.admin_console_no_output'),
                );
        }

        return back()
            ->with('console_success', true)
            ->with(
                'console_output',
                $output !== '' ? $output : __('roleui.admin_console_done'),
            );
    }
}
