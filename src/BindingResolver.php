<?php

namespace Laravel\Wayfinder;

use Illuminate\Database\Eloquent\Model;

class BindingResolver
{
    protected static array $booted = [];

    protected static array $columns = [];

    public static function resolveTypeAndKey(string $routable, string|null $key = null): array
    {
        $booted = self::$booted[$routable] ??= app($routable);

        $key ??= $booted->getRouteKeyName();

        if (! ($booted instanceof Model)) {
            return [null, $key];
        }

        self::$columns[$routable] ??= $booted->getConnection()->getSchemaBuilder()->getColumns($booted->getTable());

        return [
            collect(self::$columns[$routable])->first(
                fn ($column) => $column['name'] === $key,
            )['type_name'] ?? null,
            $key,
        ];
    }
}
