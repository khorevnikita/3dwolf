<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <style>
        @font-face {
            font-family: "Dojo Sans Serif";
            font-style: normal;
            font-weight: normal;
            src: url(http://example.com/fonts/dojosans.ttf) format('truetype');
        }

        * {
            font-family: "Dojo Sans Serif", "DejaVu Sans", sans-serif;
            font-size: 8px;
            line-height: 6px
        }

        h1 {
            font-size: 12px;
            line-height: 10px;
            margin-bottom: 3px;
        }

        p {
            margin-bottom: 1px;
            /* max-height: 8px;
             overflow: hidden;*/
            white-space: nowrap;
        }

        @page {
            margin: 5px 5px 5px 5px;
        }

    </style>
</head>
<body>
<h1>{{$part->inv_number}}</h1>
<p>{{$part->prod_number}} ({{$part->material?->name}} {{$part->weight?:0}}гр.)</p>
<p>{{$part->manufacturer?->name}}</p>
<p>{{$part->color}}</p>
</body>
</html>
