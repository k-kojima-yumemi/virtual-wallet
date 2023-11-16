<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Balance</title>
    <link href="{{asset('/all.css')}}" rel="stylesheet">
</head>
<body>

<div class="content">
    <header>
        今の残高
    </header>
    <div class="wrap">
        <div id="balance-text" class="balance-text"></div>
    </div>
    <div class="wrap">
        <button class="circle btn " onclick="location.assign('/logs')">使用履歴</button>
    </div>
    <div class="wrap-flex">
        <div class="inner-flex">
            <button class="circle btn" onclick="location.assign('/charge')">チャージ</button>
        </div>
        <div class="inner-flex">
            <button class="circle btn" onclick="location.assign('/use')" id="use_button">使用</button>
        </div>
    </div>
</div>
</body>
<script>
    const balanceText = document.getElementById("balance-text");
    const useButton = document.getElementById("use_button");
    fetch("/api/balance")
        .then((response) => response.json())
        .then((json) => {
            console.log(json);
            let balance = json["balance"];
            balanceText.textContent = formatBalance(balance);
            if (balance <= 0) {
                useButton.disabled = true;
                useButton.title = "使用するにはまずチャージしてください"
            }
        });

    function formatBalance(balance) {
        const formatter = new Intl.NumberFormat("ja-JP", {
            "style": "currency",
            "currency": "JPY",
            "currencyDisplay": "name",
        });
        return formatter.format(balance);
    }
</script>
</html>