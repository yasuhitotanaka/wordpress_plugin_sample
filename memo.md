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

# Option API

### update_option, get_option

```php
update_option('my_color', 'Blue');
$color = get_option('my_color'); // Blue

$colors = array('Blue', 'Green', 'Yellow', 'Red');
update_option('my_color', $colors);

delete_option('my_color');
```

```php
// 第４引数には、Wordpressに自動的に値を取得するかどうか'yes', 'no'で指定ができる（デフォルトは'yes'）
update_option('my_color', 'Green', false, 'no');
```

## Transients API

### set_transient

JSONの一時保存などに使われることが多い

```php
// 第三引数に秒を指定できる。下の例だと、3600秒あとは無効になる
set_transient('my_color', 'Green', 3600);
```

＊ 45文字以下の文字列しか使用できない
＊ オートロード機能がないので、使いすぎるとパフォーマンス低下につながる恐れがある

## Metadata API

メタデータ API
https://wpdocs.osdn.jp/%E3%83%A1%E3%82%BF%E3%83%87%E3%83%BC%E3%82%BF_API

### メタデータの取得(get_post_meta)

#### 仕様

$post_id: 任意の記事ID
$meta_key：メタデータのキー
$single：true=>単独の値を返す、false=>配列を返す（デフォルトはfalse）

```php
$meta_values = get_post_meta($post_id, $meta_key, $single);
```

#### 使い方

```php
update_post_meta(100, 'sub_title', 'Hello World!');
$meta_value = get_post_meta(100, 'sub_title');
```

### メタデータの追加(update_post_meta)

#### 仕様

$post_id: 任意の記事ID
$meta_key：メタデータのキー
$meta_value：メタデータの値
$unique：ユニークか否か

```php
update_post_meta($post_id, $meta_key, $meta_value, $unique);
```

#### 使い方

```php
//同じメタキーに複数の値を保存することも可能
update_post_meta(100, 'sub_title', 'Hello World!');
update_post_meta(100, 'sub_title', 'Hello World!555');
```
＊ $uniqueをtrueにした場合は、単一の値しかセットできなくなる


### メタデータの削除(delete_post_meta)

#### 仕様

$post_id: 任意の記事ID
$meta_key：メタデータのキー
$meta_value：メタデータの値

```php
delete_post_meta($post_id, $meta_key, $meta_value);
```

#### 使い方

```php
// メタキーに不随しているすべての値を削除
update_post_meta(100, 'sub_title');

// メタキーに不随している特定の値を削除
update_post_meta(100, 'sub_title', 'Hello World!555');
```

## HTTP API

外部サーバーに対してHTTP接続を行うためのもの

HTTP API
https://wpdocs.osdn.jp/HTTP_API

### wp_remote_get()

指定されたURLに対してGETメソッドを使用

### 仕様

```php
$response = wp_remote_get($url, $args);
```

### 使用例

```php
$url = 'https://example.com/api.json';
$response = wp_remote_get(esc_url_raw($url));
```

上の結果には、以下の関数を使うとさまざまな値をとれる

 - wp_remote_retrive_body()
 - wp_remote_retrive_header()
 - wp_remote_retrive_headers()
 - wp_remote_retrive_response_code() //ステータスコード
 - wp_remote_retrive_response_message() //ステータスメッセージ

### エラー処理の方法

```php
$response = wp_remote_get('https://example.com/api.json');

if (! is_wp_error($response)
    && wp_remote_retrive_response_code($response) == 200) {
      $response_body = wp_remote_retrive_body($response);
    } else {
      // Error処理
    }
```

## Rewrite API

URLパターンの処理を追加 (Djangoでいうurls.py)

### add_rewrite_endpoint()

```php
/*
Plugin Name: My Rewrite Rules
*/

register_activation_hook(__FILE__, 'my_activation_callback');

function my_activation_callback() {
  add_rewrite_endpoint('json', EP_ROOT); // rewriteルールの追加
  flush_rewrite_rules(); // rewriteルールのキャッシュを初期化
}

add_action('init', 'my_init');

function my_init() {
  add_rewrite_endpoint('json', EP_ROOT);
}

// JSONを出力する
add_action('template_redirect', 'my_template_redirect');

function my_template_redirect() {
  global $wp_query;

  if (isset($wp_query->query['json'])) {
    if (! $wp_query->query['json']) {
      header("Content-type: application/json; charset=utf-8");
      $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish'
      ));
      echo json_encode($posts);
      exit;
    } else {
      $wp_query->set_404();
      status_header(404);
      return;
    }
  }
}
```

## Widgets API

plugins/my-first-widget/my-first-widget-php

```php
Plugin Name: My Rewrite Widget

add_action(
  'widgets_init',
  create_function('', 'return register_widget("My_Widget");')
);

class My_Widget extends WP_Widget {

  function __construct() {
    $widget_ops = array('description' => 'My First Widget');
    $control_ops = array('width' => 400, 'height' => 350);

    parent::__construct(
      false,
      'My First Widget',
      $widget_ops,
      $control_ops
    );
  }

  public function form($par) {

  }

  public function update ($new_instance, $old_instance) {
    return $new_instance;
  }

  public function widget($args, $par) {
    echo $args['before_widget'];
    echo $args['before_title'];
    echo esc_html($par['title']);

    echo $args['after_title'];
      echo esc_html($par['text']);
    echo $args['after_widget'];
  }

  public function form($par) {
    // タイトルの入力
    if (isset($par['title']) && $par['title']) {
      $title = $par['title'];
    } else {
      $title = '';
    }

    $id = $this->get_field_id('title');
    $name = $this->get_field_name('title');

    echo '<p>';
    echo 'タイトル:<br />';
    printf(
      '<input type="text" id="%s" name="%s" value="%s">',
      $id,
      $name,
      esc_attr($title)
    );
    echo '</p>';
  }

  //コンテンツの入力
  if (isset($par['text']) && $par['text']) {
    $text = $par['text'];
  } else {
    $text = "";
  }

  $id = $this->get_field_id('text');
  $name = $this->get_field_name('text');

  echo '<p>';
  echo 'コンテンツ:<br />';
  printf(
    '<textarea id="%s" name="%s">%s</textarea>',
    $id,
    $name,
    esc_textarea($text);
  );
  echo '</p>';

}

```

## ショートコードAPI

```php
// my-1stを定義
function my_1st_func() {
  return "<p>はじめてのショートコード！</p>";
}
add_shortcode('my-1st', 'my_1st_func');


// my-2ndを定義
function my_2nd_func($atts) {

  // デフォルト値を設定
  $default_atts = array(
    'text' => 'default text'
  );
  $mearged_atts = shortcode_atts($default_atts, $atts);
  extract($mearged_atts);

  return '<p>' . esc_html($text) .'</p>';
}
add_shortcode('my-2nd', 'my_2nd_func');


// my-3rdを定義
function my_3rd_func($atts, $content='') {

  if (!$content) return;
  extract(shortcode_atts(
    array(
      'class' => 'default'
    ), $atts
  ));

  return '<p class="' . esc_attr($class) . '">' . esc_html($content) .'</p>';
}
add_shortcode('my-3rd', 'my_3rd_func');

```

関数リファレンス/shortcode atts
https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/shortcode_atts

## 実例：投稿欄にソースコードをきれいに表示させるためのコード

```php
add_filter('the_content', 'my_pre_shortcode', 7);

function my_pre_shortcode($content) {
  global $shortcode_tags;

  $shortcode_tags_org = $shortcode_tags;
  remove_all_shortcodes();
  add_shortcode('code', 'code_shortcode_handler');
  $content = do_shortcode($content);
  $shortcode_tags = $shortcode_tags_org;

  return $content;
}

function code_shortcode_handler($atts, $content = null) {
  extract(shortcode_atts(array(
      'class' => 'code',
      'encode' => 'true',
    ), $atts
  ));

  if ('true' === strtolower($encode)) {
    $content = htmlentities($content, ENT_QUOTES, get_option('blog_charset'));
  }

  return sprintf(
    '<pre class="%s"><code>%s</code></pre>' . "\n\n",
    esc_attr($class),
    esc_html($content)
  );
}

```

上のようなコードになっている理由

the_contentでの処理結果される前にwpautop()が起動してコードを整形している。
そうすると、pタグなどが貼り付けたいコードに自動的に付与される。
なので、the_contentが動く前にcode_shortcode_handler()を実行してソースコードをあらかじめ生成しておく

なので、
 1. 優先度7のmy_pre_shortcode()を呼び出す
 2. 優先度10のthe_contentフィルタでwpautop()が実行
 3. 優先度11のdo_shortcode()を呼び出す


関数リファレンス/wpautop
https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/wpautop

## Databse APIとwpdbクラス

関数リファレンス/wpdb Class
https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/wpdb_Class

### SQLインジェクションからの保護

|メソッド|内容|
|---|---|
|$wpdb->prepare()|プレースホルダを用いてSQLエスケープを行いながらクエリを作成|
|$wpdb->insert()|安全に行を挿入|
|$wpdb->update()|安全に行を更新|


サンプルコード

```php
$wpdb->get_var {
  $wpdb->prepare {
    "SELECT some FROM wp_custom WHERE foo = %s and status= %d",
    $name,
    $status
  }
};

$wpdb->insert(
  'wp_custom',
  array(
    'column1' => 'value1',
    'column2' => 123
  ),
  array(
    '%s', // 'value1'に該当
    '%d'  // 123に該当
  )
);

$wpdb->update(
  'wp_custom',
  array(
    'column1' => 'value1',
    'column2' => 123
  ),
  array(
    'ID' => 1
  ),
  array(
    '%s', // 'value1'に該当
    '%d'  // 123に該当
  ),
  array('%d') // IDに該当
);
```

### SELECT系のメソッド

|メソッド|内容|
|---|---|
|$wpdb->get_var()|変数を取得|
|$wpdb->get_row()|列を取得|
|$wpdb->get_col()|行を取得|
|$wpdb->get_results()|一般的な結果の取得|
|$wpdb->delete()|行の削除|

### プロパティの設定関数

|メソッド|内容|
|---|---|
|$wpdb->show_errors|エラーを出力するか。デフォルトはtrue|
|$wpdb->num_queries|実行されたqueryの数|
|$wpdb->num_rows|所得できたrowの数|
|$wpdb->last_query|最後に実行されたquery|
・・・

### テーブルの作成方法

```php
register_activation_hook(__FILE__, 'my_activation');

function my_activation() {
  global $wpdb;
  $table_name = $wpdb->prefix.'mytable';
  if ($wpdb->get_var("show tables like '$table'") != $table
    || get_option('my_plugin_table_version') !== MY_PLUGIN_TABLE_VERSION) { //MY_PLUGIN_TABLE_VERSION: 現在のTableのバージョン
    $sql = "CREATE TABLE " . $table_name . "(
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      time BIGINT(11) DEFAULT '0' NOT NULL,
      name TINYTEXT NOT NULL,
      text TEXT NOT NULL,
      url VARCHAR(55) NOT NULL,
      UNIQUE KEY id (id)
    );";

    require_once(ABSPATH, 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
```

dbDelta()の注意点

 1. 1行につき1つのフィールドを定義する
 2. PRIMARY KEYと主キーの定義の間には2つのスペースが必要
 3. INDEXという言葉ではなく、KEYという言葉を使う
