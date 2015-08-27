<!DOCTYPE html>
<html style="padding: 0; margin: 0; width: 100%; height: 100%;">
<head>
    <title><?= $message ?><?php if ($code): ?> (<?= $code ?>)<?php endif; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            font-size: 16px;
            font-family: 'Trebuchet MS', sans-serif;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .flex {
            display: flex;
            width: 100%;
            height: 100%;
            vertical-align: middle;
            text-align: center;
            justify-content: center;
            align-items: center;
        }

        a, a:active, a:hover, a:visited {
            color: inherit;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            display: block;
            margin: 0;
            clear: both;
            font-size: 3rem;
            line-height: 5rem;
        }
    </style>
</head>
<body>
<div class="flex">
    <div>
        <p>
            <?= $message ?>
            <?php if ($code): ?><span class="code"> - <?= $code ?></span><?php endif; ?>
        </p>

        <p>Go <a href="javascript:history.go(-1);">back</a> or <a href="/">home</a></p>
    </div>
</div>
</body>
</html>