## WP CLIのコマンド集

### WP CLIとは？
 wordpressの操作をコマンドラインからできるツール

```
# plugin control
wp plugin install {wordpress plugin}
wp plugin status
wp plugin update --all

# theme control
wp theme update --all

#database control
wp db export backup-sql
wp db import backup-sql

# search and replace
wp search-replace 'http://example.com' 'http://update-example.com' => 引数１から2へ更新

# debbug (example)
wp eval 'echo WP_CONTENT_DTR;'
```


## デバッグ方法

```php
# disable
define('WP_DEBUG', false);

# enable
define('WP_DEBUG', true);
```


## プラグイン開発用デバッグツール、プラグイン

 - Debug Bar
 - Pluginception
 - Developer


## テストツール

 - Grunt
 - Wordpress i18n Tools
 - PHPUnit
 - PHP_CodeSniffer

## プラグインのディレクトリ構造

 my_plugin
  - css (directory)
  - includes (directory)
  - js (directory)
  - my_plugin.php

## プラグインヘッダ

```php
/*
 Author:
 Author URI:
 Description:
 Domain Path:
 Plugin Name: My First Plugin
 Plugin URI:
 Text Domain:
 Version:
*/
add_action('some_book', 'my_first_plugin_action');

function my_first_plugin_action() {
  //Code...
}
```

## コーディングスタンダードの確認方法

### PHPの場合

PHP_CodeSnifferを使う


## クラスのメソッドを登録する場合（例）

```php
class PluginNameClass {
  public function __construct() {
    add_action('wp_head', array($this, 'action'));
    add_filter('the_content', array($this, 'filter'));
  }

  public function action() {
    // Code
  }

  public function filter() {
    // Code
  }

}

new PluginNameClass();
```
