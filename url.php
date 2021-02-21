<?PHP
$URL = $_POST["url"];
if (preg_match("/(https:\/\/docs\.google\.com\/forms\/)/",$URL)) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //レスポンスを表示するか
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  //"Location: " ヘッダの内容をたどる
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);  //"Location: " ヘッダの内容をたどる深さ
    //ここまでオプション

    $output = curl_exec($ch) or die('error ' . curl_error($ch)); 
    curl_close($ch);
    mb_language("Japanese");
    $html_source = mb_convert_encoding($output, "UTF-8", "auto");
    $html_source = strstr($html_source, 'LOAD_DATA_');
    $html_source = strstr($html_source, '"/forms"',true);

    $keywords = preg_split("/((\,\[\[)|(\]\s+?\]\s+?\]\s+?\,\[)|(\]\s+?\]\s+?\]\s+?\]))/", $html_source);
    $keywords_copy = $keywords;
    for ($i = 0 ; $i < count($keywords); $i++){
        $keywords[$i] = preg_split("/(\,)/", $keywords[$i]);
    }
    $row = 1;
    $content = '';

}else{
echo("不正");
}
function rees($str){
    $str = preg_replace("/[\"\[\]]/", "", $str);
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
function actionurl($str){
    $str = preg_replace("/(viewform)/", "formResponse", $str);
    $str = preg_replace("/(\?usp\=sf\_link)/", "", $str);
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
<title>管理用ページ</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.5.13/dist/css/uikit.min.css" />
<script src="https://cdn.jsdelivr.net/npm/uikit@3.5.13/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.5.13/dist/js/uikit-icons.min.js"></script>
</head>
<body class="uk-container container">
<br><br>

<div class="uk-card uk-card-hover uk-card-default" id="content">
    <div class="uk-card-header">
    <h2>プレビュー(UIkit)</h2>
    </div>
    <div class="uk-card-body">
        <form class="uk-form-horizontal uk-margin-large" action="<?=actionurl($URL)?>" uk-margin>
            <p><span class="uk-text-danger text-danger">*</span>：必須</p>
            <?php
            $row = 1;
            while($row < count($keywords_copy)){
                if($keywords[$row][3] == "0"){
                    echo("\n");
                    ?>
                    <div class="row my-4">
                        <label class="uk-form-label col-md-2" for="content-<?php echo($row); ?>"><?=rees($keywords[$row][1])?><?php if($keywords[$row + 1][2] == "1") echo(' <span class="uk-text-danger text-danger">*</span>'); ?></label>
                        <div class="uk-form-controls col-md-10">
                            <input class="uk-input form-control" id="content-<?php echo($row); ?>" type="text" placeholder="<?=rees($keywords[$row][1]) ?><?php $row++;?>" name="entry.<?=rees($keywords[$row][0])?>" <?php if($keywords[$row][2] == "1")echo("required"); ?>>
                        </div>
                    </div><?php
                }else if($keywords[$row][3] == "1"){
                    echo("\n");
                    ?>
                    <div class="row my-4">
                        <label class="uk-form-label col-md-2" for="content-<?php echo($row); ?>"><?=rees($keywords[$row][1])?><?php if($keywords[$row + 1][2] == "1") echo(' <span class="uk-text-danger text-danger">*</span>'); ?></label>
                        <div class="uk-form-controls col-md-10">
                            <textarea class="uk-textarea form-control" id="content-<?php echo($row); ?>" rows="3" placeholder="<?=rees($keywords[$row][1]) ?><?php $row++;?>" name="entry.<?=rees($keywords[$row][0])?>" <?php if($keywords[$row][2] == "1")echo("required"); ?>></textarea>
                        </div>
                    </div><?php
                }else if($keywords[$row][3] == "2"){
                    echo("\n");
                    ?>
                    <div class="row my-4">
                        <label class="uk-form-label col-md-2" for="content-<?php echo($row); ?>"><?=rees($keywords[$row][1])?><?php if($keywords[$row + 1][2] == "1") echo(' <span class="uk-text-danger text-danger">*</span>'); ?></label>
                        <div class="uk-form-controls col-md-10">
                            <select class="uk-select form-select" id="form-<?php echo($row); $row++; ?>" name="entry.<?=rees($keywords[$row][0])?>">
                                <option selected>選択してください</option>
                                <?php 
                                $row++;
                                $col = 0;
                                while($col < count($keywords[$row])){
                                    if(!(empty($keywords[$row][$col])) && $keywords[$row][$col] != "null"){
                                    ?><option value="<?=rees($keywords[$row][$col]) ?>"><?=rees($keywords[$row][$col]) ?></option><?php
                                    }
                                    $col += 5;
                                }?>
                            </select>
                        </div>
                    </div><?php
                }
                $row++;
            }
            echo("\n");
            ?>
        <button type="submit" class="uk-button uk-button-primary btn btn-primary my-2">送信</button>
        <p class="uk-text-muted text-black-50">Created by Rikuya using Google Forms.This is not created or endorsed by Google.  <a href="https://policies.google.com/terms">利用規約</a> / <a href="https://policies.google.com/privacy">プライバシーポリシー</a></p>
        </form>
    </div>
</div>

<br>
<div class="uk-card uk-card-hover uk-card-default uk-card-body">
    <p>気に入りましたか？</p>
    <p>ダウンロードすることができます。</p>
    <a id="download" class="uk-button uk-button-primary" href="#" download="test.html" onclick="handleDownload()">ダウンロード</a>
</div>
<br><br>

<script type='text/javascript'>
    function handleDownload() {
        var content = '<!DOCTYPE html><html><head><title>管理用ページ</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.5.13/dist/css/uikit.min.css" /></head><body class="uk-container container">';
        content += document.getElementById('content').innerHTML;
        content += '</body></html>';
        var blob = new Blob([ content ], { "type" : "text/html" });
        if (window.navigator.msSaveBlob) { 
            window.navigator.msSaveBlob(blob, "test.html");
            window.navigator.msSaveOrOpenBlob(blob, "test.html"); 
        } else {
            document.getElementById("download").href = window.URL.createObjectURL(blob);
        }
    }
</script>
</body>
</html>