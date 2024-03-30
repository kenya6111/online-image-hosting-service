
<div>
    <?php if (!empty($errors)) : ?>
        <?php foreach ($errors as $error) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div id="editor" style="width: 100%; height: 80vh; border: 1px solid slategray; position:relative">
    <div id="placeholder" style="position: absolute; top:0; left:0; z-index:1; color:slategray">ここにコードを書いてください</div>
</div>
<form id="editorForm" action="register" method="post">

    <label for="title">Title:</label>
    <input  id="title" name="title" />

    <textarea  id="text" name="text" style="display: none;"></textarea>
    <label for="syntax">Code:</label>
    <select id="syntax" name="syntax">
    <option value="html">HTML</option>
    <option value="css">CSS</option>
    <option value="javascript">Ja\vaScript</option>
    <option value="json">JSON</option>
    <option value="typescript">TypeScript</option>
    <option value="python">Python</option>
    <option value="java">Java</option>
    <option value="csharp">C#</option>
    <option value="cpp">C++</option>
    <option value="php">PHP</option>
    <option value="ruby">Ruby</option>
    <option value="go">Go</option>
    <option value="markdown">Markdown</option>
    <option value="sql">SQL</option>
    <option value="xml">XML</option>
    <option value="yaml">YAML</option>
    </select>
    <label for="expire">Expire Date:</label>
    <select id="expire" name="expire">
        <option value="tenMin">10 min</option>
        <option value="oneHour">1 hour</option>
        <option value="oneDay">1 day</option>
        <option value="never">never</option>

    </select>
    <button type="submit">Register</button>
</form>

<form id="send-form" method="post" enctype="multipart/form-data">
    <input type="file" name="file1"><br />
    <input type="submit" value="送信する" />
</form>


<!-- Monaco Editorのスクリプトを読み込む -->
<script src="https://cdn.jsdelivr.net/npm/marked@3.0.7/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
    let editor;
    require.config({
        paths: {
            vs: "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs",
        },
    });
    require(["vs/editor/editor.main"], function() {
        editor = monaco.editor.create(document.getElementById("editor"), {
            value: "",
            language: "markdown",
        });

        editor.onDidChangeModelContent(function() {

            // ユーザーがエディターに記入したらプレイスホルダーを消す
            var placeholder = document.getElementById("placeholder");
            if (editor.getValue()) {
                placeholder.style.display = "none";
            } else {
                placeholder.style.display = "block";
            }

                    // 登録フォームにコードを格納する
                    var codeInput = document.getElementById('text');
                    codeInput.value = editor.getValue();
                    console.log(editor.getValue());

        });

    });
    // syntaxセレクトボックスの値が変更されたときにエディタの言語を変更
    document.getElementById("syntax").addEventListener("change", function() {
        var selectedLanguage = this.value;
        if (editor) {
            monaco.editor.setModelLanguage(editor.getModel(), selectedLanguage);
        }
    });



    $(function() {
	
    /* 送信処理 */
    $('#send-form').submit(function() { // submit押下時に実行する

  
        /* 画像ファイルの取得・セット */
        var fd = new FormData();
        var fd = new FormData($('#send-form').get(0));
  
        /* その他フォームデータのセット */
        fd.append('name', title); // 名前
              
        /* Ajax経由で画像登録 */
        $.ajax({	
              
          url: 'api/register', // 画像登録処理を行うPHPファイル
          type: 'POST',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'html'
  
        }).done(function(data){
            console.log(data)
            var url="http://127.0.0.1:8000/show?url="+data.substr(13,16)
            location.href=url
 
 
        });

      //return false; // submitをキャンセル
  
    });
  
  });
</script>