<?php
define(
    'TURNSTILE_SITE_KEY',
    '0x4AAAAAADp1NonOnccCYXZy'
);

define(
    'TURNSTILE_SECRET',
    '0x4AAAAAADp1NpaATnwPLyDQsUYu6yAtJmU'
);

define(
    'EMAIL_CONFIG_KEY',
    getenv('EMAIL_CONFIG_KEY')
        ?: hash('sha256', TURNSTILE_SECRET . '|talentos-smtp-v1')
);

define(
    'TWO_FACTOR_KEY',
    getenv('TWO_FACTOR_KEY')
        ?: hash('sha256', EMAIL_CONFIG_KEY . '|talentos-2fa-v1')
);
