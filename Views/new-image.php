
<div>
    <?php if (!empty($errors)) : ?>
        <?php foreach ($errors as $error) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<form id="send-form" method="post" enctype="multipart/form-data">
    <label for="file1"></label>
    <div class="text-align-center">
        <input type="file" id="file1" name="file1"><br />
    </div>
    <input type="submit" value="送信する" />
</form>
<!-- <dialog id="progress-window">
        <article class="modal">
            <h4>please wait...</h4>
            <progress></progress>
        </article>
</dialog> -->
<div id="modal-container"></div>

<!-- Monaco Editorのスクリプトを読み込む -->
<script src="https://cdn.jsdelivr.net/npm/marked@3.0.7/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
    //const modal = document.getElementById('modal');
    const progressWindow = document.getElementById('progress-window');

    document.getElementById('send-form').addEventListener('submit',function(event){
        event.preventDefault();

        let fileInput=document.querySelector("#file1");
        const formData = new FormData();
        formData.append('file1',fileInput.files[0]);
        //progressWindow.showModal();

        fetch('register', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                     //progressWindow.close;

                    const shared_url = data.shared_url;
                    const delete_url = data.delete_url;

                    const container = document.getElementById('modal-container');
                    container.innerHTML = `
                    <div id="test">
                        <div >
                            <h3 class="mt-2">Upload Complete!</h3>     
                            <p>共有用URL:<br><a href="${shared_url}" target="_blank" rel="noopener">${shared_url}</a></p>
                            <p>削除用URL:<br><a href="${delete_url}" target="_blank" rel="noopener">${delete_url}</a></p>
                        </div>
                    </div>
                    `;
                } else {
                    progressWindow.open = false;
                    alert(data.message);
                }
            })
            .catch(error => {
                progressWindow.open = false;
                alert(error);
            });

    })



    // $(function() {
	
    // /* 送信処理 */
    // $('#send-form').submit(function() { // submit押下時に実行する

  
    //     /* 画像ファイルの取得・セット */
    //     var fd = new FormData();
    //     var fd = new FormData($('#send-form').get(0));
  
    //     /* その他フォームデータのセット */
    //     fd.append('name', title); // 名前
              
    //     /* Ajax経由で画像登録 */
    //     $.ajax({	
              
    //       url: 'api/register', // 画像登録処理を行うPHPファイル
    //       type: 'POST',
    //       data: fd,
    //       cache: false,
    //       contentType: false,
    //       processData: false,
    //       dataType: 'html'
  
    //     }).done(function(data){
    //         console.log(data)
    //         var url="http://127.0.0.1:8000/show?url="+data.substr(13,16)
    //         location.href=url
 
 
    //     });

    //   //return false; // submitをキャンセル
  
    // });
  
  //});
</script>