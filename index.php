<?php

require_once './vendor/autoload.php';

use Http\Api;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request = json_decode(file_get_contents('php://input'));

    if (!isset($request->keyword)) {
        return false;
    }
    header('Content-Type: application/json');

    $keyword =  $request->keyword;
    $api = new Api($keyword);
    return $api->handleApi($request->namespace);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST" action="" id="form">
        <input name="keyword" type="search">
        <button>Submit</button>
    </form>
    <div id="result"></div>
    <script>
        const before = <?= json_encode((new Api())->before) ?>;
        const form = document.querySelector('#form');
        const input = document.querySelector('input[name="keyword"]');
        const result = document.querySelector('#result');

        form.addEventListener('submit', e => {
            e.preventDefault();
            let keywd = input.value;
            let html = '';
            for (i = 0; i < before.length; i++) {
                fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            keyword: keywd,
                            namespace: before[i]
                        })
                    }).then(response => response.json())
                    .then(result => {
                        console.log(result);
                    })
            }
        })
        // const handleSubmit = (e) => {
        //     e.preventDefault();

        //     let {
        //         keyword
        //     } = e.currentTarget.value;

        //     console.log(keyword);
        // }
    </script>
</body>

</html>