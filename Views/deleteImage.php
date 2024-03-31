<div>
    <div>
        <p><a href="<?php $shared_url?>"><?php $shared_url?></a>削除する場合は以下の削除ボタンを押してください</p>
        <button id="deletebutton">削除</button>
    </div>
</div>

<script>
    document.getElementById('deletebutton').addEventListener('click',function(event){
        //event.preventDefault();
        fetch('/delete2', {
                method: 'POST',
                body: JSON.stringify({
                    'shared_url': '<?= $shared_url ?>'
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // confirmModal.open = false;
                    // successfulModal.open = true;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

    })
</script>