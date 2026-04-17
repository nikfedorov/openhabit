<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\TelescopeServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

describe('TelescopeServiceProvider', function (): void {
    beforeEach(function (): void {
        Telescope::$filterUsing = [];
        Telescope::$hiddenRequestParameters = [];
        Telescope::$hiddenRequestHeaders = [];
    });

    describe('register', function (): void {
        it('registers telescope filter', function (): void {
            $provider = new TelescopeServiceProvider($this->app);

            $provider->register();

            expect(Telescope::$filterUsing)->toHaveCount(1);
        });

        it('allows all entries in local environment', function (): void {
            $this->app['env'] = 'local';

            $provider = new TelescopeServiceProvider($this->app);
            $provider->register();

            $entry = Mockery::mock(IncomingEntry::class);
            $filter = Telescope::$filterUsing[0];

            expect($filter($entry))->toBeTrue();
        });

        it('allows entries matching specific conditions in non-local environment', function (array $expectations): void {
            $this->app['env'] = 'testing';

            $provider = new TelescopeServiceProvider($this->app);
            $provider->register();

            $entry = Mockery::mock(IncomingEntry::class);
            foreach ($expectations as $method => $returnValue) {
                $entry->shouldReceive($method)->andReturn($returnValue);
            }

            $filter = Telescope::$filterUsing[0];

            expect($filter($entry))->toBeTrue();
        })->with([
            'reportable exception' => [['isReportableException' => true]],
            'failed request' => [['isReportableException' => false, 'isFailedRequest' => true]],
            'failed job' => [['isReportableException' => false, 'isFailedRequest' => false, 'isFailedJob' => true]],
            'scheduled task' => [['isReportableException' => false, 'isFailedRequest' => false, 'isFailedJob' => false, 'isScheduledTask' => true]],
            'monitored tag' => [['isReportableException' => false, 'isFailedRequest' => false, 'isFailedJob' => false, 'isScheduledTask' => false, 'hasMonitoredTag' => true]],
        ]);

        it('rejects entries without special conditions in non-local environment', function (): void {
            $this->app['env'] = 'testing';

            $provider = new TelescopeServiceProvider($this->app);
            $provider->register();

            $entry = Mockery::mock(IncomingEntry::class);
            $entry->shouldReceive('isReportableException')->andReturn(false);
            $entry->shouldReceive('isFailedRequest')->andReturn(false);
            $entry->shouldReceive('isFailedJob')->andReturn(false);
            $entry->shouldReceive('isScheduledTask')->andReturn(false);
            $entry->shouldReceive('hasMonitoredTag')->andReturn(false);

            $filter = Telescope::$filterUsing[0];

            expect($filter($entry))->toBeFalse();
        });
    });

    describe('gate', function (): void {
        it('defines viewTelescope gate', function (): void {
            $provider = new TelescopeServiceProvider($this->app);

            $reflection = new ReflectionMethod($provider, 'gate');
            $reflection->invoke($provider);

            expect(Gate::has('viewTelescope'))->toBeTrue();
        });

        it('denies viewTelescope gate for users outside the allow list', function (): void {
            $user = User::factory()->create();
            $provider = new TelescopeServiceProvider($this->app);

            $reflection = new ReflectionMethod($provider, 'gate');
            $reflection->invoke($provider);

            expect(Gate::forUser($user)->check('viewTelescope'))->toBeFalse();
        });
    });

    describe('hideSensitiveRequestDetails', function (): void {
        it('returns early in local environment', function (): void {
            $this->app['env'] = 'local';

            $provider = new TelescopeServiceProvider($this->app);

            $reflection = new ReflectionMethod($provider, 'hideSensitiveRequestDetails');
            $reflection->invoke($provider);

            expect(Telescope::$hiddenRequestParameters)->toBeEmpty()
                ->and(Telescope::$hiddenRequestHeaders)->toBeEmpty();
        });

        it('hides sensitive request details outside local environment', function (): void {
            $this->app['env'] = 'production';

            $provider = new TelescopeServiceProvider($this->app);

            $reflection = new ReflectionMethod($provider, 'hideSensitiveRequestDetails');
            $reflection->invoke($provider);

            expect(Telescope::$hiddenRequestParameters)->toContain('_token')
                ->and(Telescope::$hiddenRequestHeaders)
                ->toContain('cookie')
                ->toContain('x-csrf-token')
                ->toContain('x-xsrf-token');
        });
    });
});
