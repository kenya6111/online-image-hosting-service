<?php
spl_autoload_extensions(".php");
spl_autoload_register();
header("Access-Control-Allow-Origin: *");
require_once "vendor/autoload.php";
$DEBUG = true;

// ルートを読み込みます。
$routes = include('Routing/routes.php');
// リクエストURIを解析してパスだけを取得します。
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');//指定した文字を先頭からのみ消す

$path2= explode('/',$path)[1];//指定文字で区切る。連想配列にする。
$method= $SERVER['REQUEST_METHOD'];

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $path)) {
    // パスにマッチする静的ファイルが存在するかチェック
    printf(__DIR__);
    if (file_exists(__DIR__ . '/' . $path)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($path);
        header("Content-Type: " . $mimeType);
        readfile(__DIR__ . '/' . $path);
        return;
    } else {
        // ファイルが存在しない場合は404
        http_response_code(404);
        echo "404 Not Found: The requested file was not found on this server.";
        return;
    }
}
// ルートにパスが存在するかチェックする
if (isset($routes[$path])) {
    // コールバックを呼び出してrendererを作成します。
    $renderer = $routes[$path]();

    try{
        // ヘッダーを設定します。
        foreach ($renderer->getFields() as $name => $value) {
            // ヘッダーに対する単純な検証を実行します。
            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
            } else {
                // ヘッダー設定に失敗した場合、ログに記録するか処理します。
                // エラー処理によっては、例外をスローするか、デフォルトのまま続行することもできます。
                http_response_code(500);
                if($DEBUG) print("Failed setting header - original: '$value', sanitized: '$sanitized_value'");
                exit;
            }

            print($renderer->getContent());
        }
    }
    catch (Exception $e){
        http_response_code(500);
        print("Internal error, please contact the admin.<br>");
        if($DEBUG) print($e->getMessage());
    }
} else {
    // マッチするルートがない場合、404エラーを表示します。
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}