<?php

if (!function_exists('talolys_normalize_host')) {
    function talolys_normalize_host(?string $host): string
    {
        $host = strtolower(trim((string) $host));
        $host = preg_replace('#^https?://#', '', $host);

        return rtrim($host, '/');
    }
}

return [

  /*
  |--------------------------------------------------------------------------
  | Central application domains
  |--------------------------------------------------------------------------
  |
  | Comma-separated list in CENTRAL_DOMAINS env. Requests to these hosts use
  | central routes (marketing, platform admin, signup) without tenant context.
  |
  */

  'central_domains' => array_values(array_filter(array_map(
      'talolys_normalize_host',
      explode(',', env('CENTRAL_DOMAINS', 'localhost,127.0.0.1'))
  ))),

  /*
  |--------------------------------------------------------------------------
  | Tenant root domain for auto-provisioned subdomains
  |--------------------------------------------------------------------------
  */

  'tenant_root_domain' => talolys_normalize_host(env('TENANT_ROOT_DOMAIN', 'talolys.test')),

  /*
  |--------------------------------------------------------------------------
  | Custom domain DNS verification
  |--------------------------------------------------------------------------
  */

  'domain_verification_prefix' => '_talolys-verify',

  /*
  |--------------------------------------------------------------------------
  | Session key for bound tenant id
  |--------------------------------------------------------------------------
  */

  'session_tenant_key' => 'talolys_tenant_id',

  /*
  |--------------------------------------------------------------------------
  | Shared secret for HTTP cron endpoint (use tenants:run in production)
  |--------------------------------------------------------------------------
  */

  'cron_secret' => env('CRON_SECRET'),

];
