<?php

require_once "vendor/autoload.php";
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    ''=>function(): HTTPRenderer{
        //期限切れのスニペットを削除する
        //DatabaseHelper::deleteExpiredSnippet();

       // $part = DatabaseHelper::getRandomComputerPart();
        
       //['part'=>$part]
        return new HTMLRenderer('list');
    },
    'list'=>function(): HTTPRenderer{

        $snippets = DatabaseHelper::getAllSnippet();

        return new HTMLRenderer('list', ['snippets'=>$snippets]);
    },
    'create'=>function(): HTTPRenderer{

        return new HTMLRenderer('new-image', []);
    },
    'register' => function (): JSONRenderer {
        
        // エラーがなかった場合、スニペットをテーブルに登録
        // urlを生成する
        try{
            $tmpPath = $_FILES['file1']['tmp_name'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            $byteSize = filesize($tmpPath);

            $ipAddress = $_SERVER['REMOTE_ADDR'];
             /* 拡張子情報の取得・セット */
             $imginfo = getimagesize($_FILES['file1']['tmp_name']);
            if($imginfo['mime'] == 'image/jpeg'){ $extension = ".jpg"; }
            if($imginfo['mime'] == 'image/png'){ $extension = ".png"; }
            if($imginfo['mime'] == 'image/gif'){ $extension = ".gif"; }

            $extension = explode('/', $mime)[1];

            $filename = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extension;
            $uploadDir =   './uploads/'; 
            $subdirectory = substr($filename, 0, 2);
            $imagePath = $uploadDir .  $subdirectory. '/' . $filename;
            // アップロード先のディレクトリがない場合は作成
            if(!is_dir(dirname($imagePath))){
                mkdir(dirname($imagePath),0777,true);
                chmod(dirname($imagePath), 0775);
            }
            // $imagesDir =   './images/';
            // $svgfilename = 'checkmark.svg';
            // chmod(dirname($imagesDir.$svgfilename), 0775);

            // アップロードにした場合は失敗のメッセージを送る
            if(move_uploaded_file($tmpPath, $imagePath)){
                chmod($imagePath, 0664);
            }else{
                return new JSONRenderer(['success' => false, 'message' => 'アップロードに失敗しました。']);
            }



            /* 拡張子存在チェック */
            if(!empty($extension)){
                
                // /* 画像登録処理 */
                // $file_save = dirname(__FILE__, 2).'/'.'images/'; // アップロード対象のディレクトリを指定
                // //$file_path=dirname(__FILE__, 2).$file_save;
                // $file_tmp = $_FILES['file1']['tmp_name'];
                // $file_name = basename($_FILES['file1']['name']);
                // $file_save_path = dirname(__FILE__, 2) . '/images/' . $file_name; 
                // move_uploaded_file($file_tmp, $file_save_path); // アップロード処理
                // chmod($file_save_path,0664);
                

                //echo "success"; // jquery側にレスポンス
	
            } else {
                
                echo "fail"; // jquery側にレスポンス
                
            }

            $hash_for_shared_url = hash('sha256', uniqid(mt_rand(), true));
            $hash_for_delete_url = hash('sha256', uniqid(mt_rand(), true));
            $shared_url = '/' . $extension . '/' . $hash_for_shared_url;
            $delete_url = '/' .  'delete' . '/' . $hash_for_delete_url;
            $imagePathFromUploadDir = $subdirectory . '/' . $filename;
            $result = DatabaseHelper::insertImage($imagePathFromUploadDir,$_FILES['file1']['name'],$_FILES['file1']['type'],$_FILES['file1']['size'],$shared_url,$delete_url );

            if ($result) {
                return new JSONRenderer(["success" => true, "shared_url" => $shared_url, "delete_url"=> $delete_url]);
            } else {
                return new JSONRenderer(["success" => false, "message" => "データベースの操作に失敗しました。"]);
            }
            
            //return new HTMLRenderer('register-result', ["url"=>$result["url"]]);
            //return new JSONRenderer(['result'=>json_encode($result['url'])]);
            //return $result;
        }catch(Exception $e){
            return new HTMLRenderer('register-result', []);

        }
    },
    'getImage' => function(): HTMLRenderer{
            $shared_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $data = DatabaseHelper::getImageData($shared_url);

            if (!$data) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => "Page not found"]);
            }

           // if(!DatabaseHelper::updateImageData($shared_url)) return new JSONRenderer(['success' => false, 'message' => 'データベースの操作に失敗しました。']);

            $path = $data['file_path'];
            $viewCount = $data['view_count'];
            $mime = $data['mine_type'];

            return new HTMLRenderer('register-result', ['path'=> $path, 'mime' => $mime ,'viewCount' => $viewCount]);
    },
    'delete' => function() : HTMLRenderer {
            $delete_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $shared_url = DatabaseHelper::getSharedUrl($delete_url);

            if (!$shared_url) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => "Page not found"]);
            }

            return new HTMLRenderer('deleteImage', ['shared_url' => $shared_url]);
    },
    'delete2' => function () : JSONRenderer {
            $json = file_get_contents("php://input");
            $shared_url = json_decode($json, true)['shared_url'];
            $imagePath = DatabaseHelper::getImageData($shared_url)['path'];
            $deleteFromDBresult = DatabaseHelper::deleteImageData($shared_url);

            if (!$deleteFromDBresult) return new JSONRenderer(["success" => false, "message" => "データベースの操作に失敗しました"]);

            $deleteFromStorageResult = unlink('./uploads/' . $imagePath);
            if (!$deleteFromStorageResult)return new JSONRenderer(["success" => false, "message" => "画像の削除に失敗しました。"]);

            return new JSONRenderer(["success" => true]);
    },
    'show' => function (): HTTPRenderer {
        // 指定されたスニペットの表示ページ

        return new HTMLRenderer('show-image', []);
    },
    'api/register' => function () {
        // スニペットの登録→表示ページへ遷移

        //登録するスニペットのバリデーション
        // $titleRes = ValidationHelper::validateText($_POST['title'] ?? null,1,100);

        // $textRes = ValidationHelper::validateText($_POST['text'] ?? null,1,1000);

        // $syntaxRes = ValidationHelper::validateSyntax($_POST['syntax'] ?? null);

        // $expireRes = ValidationHelper::validateExpireDatetime($_POST['expire'] ?? null);
        // if (count($textRes["error"]) > 0 || count($syntaxRes["error"]) > 0 || count($expireRes["error"]) > 0) {
        //     $allErrors = array_merge($textRes["error"], $syntaxRes["error"], $expireRes["error"]);
        //     //全てのエラーをスニペット作成ページに引き渡す
        // return new HTMLRenderer('new-snippet', ['errors'=>$allErrors]);

        // }

        // エラーがなかった場合、スニペットをテーブルに登録
        // urlを生成する
        try{
             /* 拡張子情報の取得・セット */
             $imginfo = getimagesize($_FILES['file1']['tmp_name']);
            if($imginfo['mime'] == 'image/jpeg'){ $extension = ".jpg"; }
            if($imginfo['mime'] == 'image/png'){ $extension = ".png"; }
            if($imginfo['mime'] == 'image/gif'){ $extension = ".gif"; }
            
            /* 拡張子存在チェック */
            if(!empty($extension)){
                
                /* 画像登録処理 */
                $file_save = dirname(__FILE__, 2).'/'.'images/'; // アップロード対象のディレクトリを指定
                //$file_path=dirname(__FILE__, 2).$file_save;
                $file_tmp = $_FILES['file1']['tmp_name'];
                $file_name = basename($_FILES['file1']['name']);
                $file_save_path = dirname(__FILE__, 2) . '/images/' . $file_name; 
                move_uploaded_file($file_tmp, $file_save_path); // アップロード処理

                //echo "success"; // jquery側にレスポンス
	
            } else {
                
                echo "fail"; // jquery側にレスポンス
                
            }
           // $result = DatabaseHelper::insertImage(dirname(__FILE__, 2).$file_save,$_FILES['file1']['name'],$_FILES['file1']['type'],$_FILES['file1']['size'] );
            // print_r($result);
            
            //return new HTMLRenderer('register-result', ["url"=>$result["url"]]);

           // return new JSONRenderer(['result'=>json_encode($result['url'])]);
            //return $result;
        }catch(Exception $e){
            return new HTMLRenderer('register-result', []);

        }
    }
    // 'random/part'=>function(): HTTPRenderer{
    //     $part = DatabaseHelper::getRandomComputerPart();

    //     return new HTMLRenderer('random-part', ['part'=>$part]);
    // },
    // 'parts'=>function(): HTTPRenderer{
    //     // IDの検証
    //     $id = ValidationHelper::integer($_GET['id']??null);

    //     $part = DatabaseHelper::getComputerPartById($id);
    //     return new HTMLRenderer('parts', ['part'=>$part]);
    // },
    // 'api/random/part'=>function(): HTTPRenderer{
    //     $part = DatabaseHelper::getRandomComputerPart();
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'api/parts'=>function(){
    //     $id = ValidationHelper::integer($_GET['id']??null);
    //     $part = DatabaseHelper::getComputerPartById($id);
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'api/types'=>function(){
    //     $pagenum = ValidationHelper::integer($_GET['page']??null);
    //     $perpage = ValidationHelper::integer($_GET['perpage']??null);
    //     $part = DatabaseHelper::getComputerPartByType($_GET['type']);
    //     return new JSONRenderer(['part'=>$part,'pagenum'=>$pagenum,'perpage'=>$perpage]);
    // },
    // 'api/random/computer'=>function(){

    //     $part = DatabaseHelper::get5RandomComputerPart();
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'api/parts/newest'=>function(){

    //     $part = DatabaseHelper::getNewestComputerPart();
    //     return new JSONRenderer(['part'=>$part]);
    // },

    // //以下、text snippet sharing serviceのエンドポイント 
    // 'api/random/snippet'=>function(){

    //     $part = DatabaseHelper::getRandomSnippetText();
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'api/newest/snippet'=>function(){

    //     $part = DatabaseHelper::getNewestSnippetData();
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'api/registry/snippet'=>function(){
    //     header("Access-Control-Allow-Origin: *");
    //     header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    //     header("Access-Control-Allow-Headers: Content-Type");
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         // プリフライトリクエストを処理して終了
    //         exit();
    //     }
    //     // JSONからPHP配列へ変換
    //     $json = file_get_contents('php://input');
    //     $data = json_decode($json, true);
    //     $part = DatabaseHelper::registSnippetText($data['content']);
    //     return new JSONRenderer(['part'=>$part]);
    // },
    // 'snippet'=>function(): HTTPRenderer{
    //     // IDの検証
    //     $id = ValidationHelper::integer($_GET['id']??null);

    //     $part = DatabaseHelper::getComputerPartById($id);
    //     return new HTMLRenderer('parts', ['part'=>$part]);
    // },
];