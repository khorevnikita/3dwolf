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
            font-size: 10px;
            line-height: 16px
        }

        h1 {
            font-size: 12px;
            line-height: 18px;
            margin-top: 0;
            margin-bottom: 0;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 50px 30px 50px 30px;
        }

        .bordered tr td {
            border-right: 1px solid;
            border-bottom: 1px solid;
            min-height: 40px;
            height: 20px;
        }

        .bordered tr:first-of-type td {
            border-top: 1px solid;
        }

        .bordered tr td:first-of-type {
            border-left: 1px solid;
        }


    </style>
</head>
<body>
<header>

</header>
<footer>

</footer>
<main>
    <table style="margin-left: auto;text-align: right;">
        <tbody>
        <tr>
            <td></td>
            <td>
                Приложение №1<br/>
                К Договору подряда №{{$contract->number}} от {{$date['day']}} {{$date['month']}} {{$date['year']}} г.
            </td>
        </tr>
        </tbody>
    </table>
    <p>ФОРМА</p>
    <h1 style="text-align: center">ЗАДАНИЕ от {{$date['day']}} {{$date['month']}} {{$date['year']}}</h1>
    <table style="margin-top: 10px">
        <tbody>
        <tr>
            <td>
                ЗАКАЗЧИК&nbsp;
            </td>
            <td style="width: 300px;border-bottom: 1px solid;"></td>
        </tr>
        <tr>
            <td>
                ДОГОВОР&nbsp;
            </td>
            <td style="width: 300px;border-bottom: 1px solid;"></td>
        </tr>
        </tbody>
    </table>

    <h1 style="text-align: center;margin-top: 10px;">СПЕЦИФИКАЦИЯ</h1>

    <table style="font-weight: bold;text-align: center" class="bordered">
        <tbody>
        <tr>
            <td>№ п/п</td>
            <td>Наименование/ условное обозначение</td>
            <td>Описание (технические и иные характеристики)</td>
            <td>Вес, г</td>
            <td>Время печати</td>
            <td>Тираж (шт.)</td>
            <td>Цена за ед., руб.
                (с НДС/без НДС)
            </td>
            <td>Стоимость, руб.
                (с НДС/без НДС)
            </td>
        </tr>
        @for($i=0;$i<4;$i++)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
        <tr>
            <td style="text-align: right" colspan="5">ИТОГО, руб</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align: right" colspan="5">НДС, руб</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <h1 style="text-align: center;margin-top: 10px;">ДОПОЛНИТЕЛЬНЫЕ РАБОТЫ</h1>

    <table style="font-weight: bold;text-align: center;width: 100%" class="bordered">
        <tbody>
        <tr>
            <td rowspan="2">№ п/п</td>
            <td style="width: 280px" rowspan="2">Условное обозначение</td>
            <td colspan="2">3D-моделирование</td>
            <td colspan="2">Печать тестовой 3D-модели</td>
            <td colspan="3">Послепечатная обработка</td>
        </tr>
        <tr>
            <td>Да/нет</td>
            <td>Цена, руб.</td>
            <td>Да/нет</td>
            <td>Цена, руб.</td>
            <td>Да/нет</td>
            <td style="width: 280px">Описание</td>
            <td>Цена, руб.</td>
        </tr>
        @for($i=0;$i<4;$i++)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
        </tbody>
    </table>

    <table style="margin-top: 20px;width: 75%; margin-left: auto; margin-right: auto;">
        <tbody>
        <tr>
            <td style="font-weight: bold">ПОДРЯДЧИК</td>
            <td style="font-weight: bold">ЗАКАЗЧИК</td>
        </tr>
        <tr>
            <td style="height: 30px;border-bottom: 1px solid"></td>
            <td style="height: 30px;border-bottom: 1px solid"></td>
        </tr>
        </tbody>
    </table>
</main>
</body>
</html>
