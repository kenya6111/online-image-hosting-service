<?php
use Database\MySQLWrapper;



// URLクエリパラメータを通じてIDが提供されたかどうかをチェックします。
$url = $_GET['url'] ?? null;
if ($url ){
    
    // データベース接続を初期化します。
    $db = new MySQLWrapper();
    
    try {
        // IDでスニペットを取得するステートメントを準備します。
        $stmt = $db->prepare("SELECT * FROM images WHERE url = ?");
        $stmt->bind_param('s', $url);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        // 画像のファイル名をURLエンコード
        $encodedFileName = rawurlencode($image['file_name']);
        // 相対パスをそのまま使用し、エンコードされたファイル名を結合
        $imgPath = "../images/" . $encodedFileName;
        //$imgPath ="../images/".urlencode($image['file_name']);
        if (!$image){
            throw new Exception("this image does not exist");
        }

    } catch (Exception $e) {
        die("Error fetching snippet by uid: " . $e->getMessage());
    }
}else{
    die("uid is necessary." );

}


?>
<div id="" style="display: flex;  flex-direction: column;">



<div>id: <?= htmlspecialchars($image['snippet_id']) ?></div>

    <div>title: <?= htmlspecialchars($image['title']) ?></div>
    <div >syntax: <?= htmlspecialchars($image['syntax']) ?></div>
    <div>expire: <?= $image['expire_datetime'] ? htmlspecialchars($image['expire_datetime']) : "never" ?></div>

    <div id="syntax" style="display: none;"><?= htmlspecialchars($image['syntax']) ?></div>
    <div id="content" name="text" style="display: none;"><?= htmlspecialchars($image['content']) ?></div>
</div>
<img src="<?= $imgPath ?>">


<div id="editor" style="width: 100%; height: 80vh; border: 1px solid slategray; position:relative">
</div>




<!-- Monaco Editorのスクリプトを読み込む -->
<script src="https://cdn.jsdelivr.net/npm/marked@3.0.7/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
<script>



    let editor;
    require.config({
        paths: {
            vs: "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs",
        },
    });
    require(["vs/editor/editor.main"], function() {
                    // id="text" の要素からテキストを取得
                    let textContent = document.getElementById("content").innerText;
console.log("textContent",textContent);

                    let syntax = document.getElementById("syntax").innerText;
console.log("syntax",syntax);
        editor = monaco.editor.create(document.getElementById("editor"), {
            value: textContent,
            language: syntax,
            readOnly: true
        });

    });
   
</script>