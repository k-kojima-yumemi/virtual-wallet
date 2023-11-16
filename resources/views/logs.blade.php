<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Usage Logs</title>
    <link href="{{asset('/all.css')}}" rel="stylesheet">
</head>
<body>
<div class="table-content">
    <header>
        使用履歴
    </header>
    <table id="usage-table" class="usage-table">
        <thead>
        <tr>
            <th scope="col">使用日時</th>
            <th scope="col">金額</th>
            <th scope="col">目的</th>
        </tr>
        </thead>
        <tbody id="usage-table-body"></tbody>
    </table>
    <div class="date-flex">
        <div class="date-content">
            <label for="from-date">From date</label>
            <input type="date" id="from-date">
            <label for="from-time">From time</label>
            <input type="time" id="from-time">
        </div>
        <div class="date-content">
            <label for="to-date">To date</label>
            <input type="date" id="to-date">
            <label for="to-time">To time</label>
            <input type="time" id="to-time">
        </div>
    </div>
    <div class="center wrap">
        <button class="btn circle max-width" id="date-update-button">日付で絞り込み</button>
    </div>
</div>

<script>
    const tableBody = document.getElementById("usage-table-body");
    const LOG_KEYS = ["used_at", "changed_amount", "description"];

    function fetchAndFillTable(query) {
        fetch("/api/usage_logs" + query, {
            "headers": {
                "Accept": "application/json",
            },
        }).then(response => {
            if (!response.ok) {
                console.log("Caught error " + response.status);
            }
            return response.json();
        }).then(json => {
            if ("message" in json) {
                alert(`${json["message"]}`)
            } else {
                tableBody.innerHTML = "";
                json["logs"].forEach(eachLog => {
                    const row = document.createElement("tr");
                    LOG_KEYS.forEach(key => {
                        const cell = document.createElement("td");
                        const cellText = document.createTextNode(
                            key === "used_at" ? convertUTCToLocal(eachLog[key]) : eachLog[key]
                        );
                        cell.appendChild(cellText);
                        if (key === "changed_amount") {
                            cell.setAttribute("align", "right")
                        } else if (key === "description" && cell.textContent === "チャージ") {
                            // 太字にする
                            // cell.innerHTML = "<b>チャージ</b>"
                        }
                        row.appendChild(cell);
                    });
                    tableBody.appendChild(row);
                });
            }
        });
    }

    function convertUTCToLocal(dateString) {
        const utcDate = new Date(dateString + "Z");
        const localDateString = utcDate.toLocaleString(
            undefined, {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }
        );
        return localDateString;
    }

    // Initial filling, no queries
    fetchAndFillTable("");

    const updateButton = document.getElementById("date-update-button");
    updateButton.addEventListener("click", ignore => {
        let query = "?";
        const fromDate = document.getElementById("from-date").value;
        const fromTime = document.getElementById("from-time").value || "00:00:00";
        if (fromDate) {
            const localDate = new Date(fromDate + "T" + fromTime);
            query += `&from=${localDate.toISOString()}`;
        }
        const toDate = document.getElementById("to-date").value;
        const toTime = document.getElementById("to-time").value || "23:59:59.9";
        if (toDate) {
            const localDate = new Date(toDate + "T" + toTime);
            query += `&to=${localDate.toISOString()}`;
        }
        fetchAndFillTable(query);
    });
</script>
</body>
</html>
