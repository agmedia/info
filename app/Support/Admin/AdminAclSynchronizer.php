<?php

namespace App\Support\Admin;

use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class AdminAclSynchronizer
{
    public static function ensureSynced(): void
    {
        if (! self::tablesAvailable()) {
            return;
        }

        /** @var array<int, array{name:string,title:string,group:string}> $abilityDefinitions */
        $abilityDefinitions = config('admin_acl.abilities', []);
        /** @var array<string, array<int, string>> $roleDefaults */
        $roleDefaults = config('admin_acl.roles', []);

        $abilityIdsByName = [];
        $newAbilityNames = [];

        foreach ($abilityDefinitions as $definition) {
            $ability = Ability::query()
                ->where('name', $definition['name'])
                ->whereNull('entity_id')
                ->whereNull('entity_type')
                ->first();

            if (! $ability) {
                $ability = new Ability();
                $ability->name = $definition['name'];
                $newAbilityNames[] = $definition['name'];
            }

            $ability->title = $definition['title'];
            $ability->options = ['group' => $definition['group']];
            $ability->save();

            $abilityIdsByName[$definition['name']] = (int) $ability->id;
        }

        foreach (self::defaultRoles() as $roleName => $title) {
            Role::query()->firstOrCreate(
                ['name' => $roleName],
                ['title' => $title]
            );
        }

        Bouncer::allow('superadmin')->everything();

        foreach ($roleDefaults as $roleName => $abilities) {
            $role = Role::query()->where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            foreach ($abilities as $abilityName) {
                if (! in_array($abilityName, $newAbilityNames, true)) {
                    continue;
                }

                if (! isset($abilityIdsByName[$abilityName])) {
                    continue;
                }

                Bouncer::allow($role)->to($abilityName);
            }
        }

        Bouncer::refresh();
    }

    private static function tablesAvailable(): bool
    {
        return Schema::hasTable('roles')
            && Schema::hasTable('abilities')
            && Schema::hasTable('permissions');
    }

    /**
     * @return array<string, string>
     */
    private static function defaultRoles(): array
    {
        return [
            'superadmin' => 'Super Administrator',
            'admin' => 'Administrator',
            'editor' => 'Editor',
            'customer' => 'Customer',
        ];
    }
}
