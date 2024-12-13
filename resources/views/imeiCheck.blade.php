<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<!-- resources/views/form.blade.php -->
<h1>平安穗粤卡号查询系统</h1>
<h3>(内测中)</h3>
<form action="{{ route('submit.form') }}" method="POST" id="myForm">
    @csrf
    <label for="imei">请输入:</label>
    <select id="type" name="type">
        <option value="1">IMEI</option>
        <option value="2">ICCID</option>
    </select>
    <input type="text" name="key" id="key" required>
    <button type="submit">查询</button>
</form>
<div id="response"></div>

</body>
</html>


<script>
    document.getElementById('myForm').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('response').innerHTML = '查询中';
        let formData = new FormData(this);

        fetch("{{ route('submit.form') }}", {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('response').innerHTML = data.message + "<br>" + JSON.stringify(data.data)
                + "<br>" + "平安穗粤卡段IMSI： " +data.smde_imei;
            })
            .catch(error => console.error('Error:', error));
    });
</script>
