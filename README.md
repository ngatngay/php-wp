# Simple Helper for WordPress

## Install

Download and Install.

https://cdn.ngatngay.net/wp/ngatngay/plugin.zip

## Require

for theme:
```php
if (!in_array('ngatngay/plugin.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    echo 'miss plugin';
    return;
}
```

for plugin:
```php
if (!in_array('ngatngay/plugin.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	echo 'miss plugin';
    return;;
}
```