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
            font-size: 12px;
            line-height: 18px
        }

        h1 {
            font-size: 18px;
            line-height: 24px;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 170px 30px 100px 30px;
        }

        header {
            position: fixed;
            top: -150px;
            left: 0;
            right: 0;
            /*background-color: lightblue;*/
            height: 250px;
            display: flex;
            justify-content: space-between;
        }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            /* background-color: lightblue;*/
            height: 50px;
            display: flex;
        }

        /*main {
            padding-top: 100px;
        }*/

        p {
            page-break-after: always;
        }

        p:last-child {
            page-break-after: never;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .d-flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .bold {
            font-weight: bold;
        }

        .block {
            display: table;
            width: 100%;
        }

        .block > div {
            display: table-cell;;
        }

        .bordered {
            border: 1px solid;
        }

        .bordered > * {
            border-right: 1px solid;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
<header>
    <div class="block">
        <img alt="logo" src="{{storage_path('app/public/logo.png')}}"/>
        <div>
            <b>{{$settings['brand_name']}}</b><br/>
            Печать, моделирование <br/>
            {{$settings['phone']}} <br/>
            {{$settings['website']}} <br/>
            {{$settings['email']}}
        </div>
    </div>
</header>
<footer>
    <div>
        ПОДРЯДЧИК _________________________
    </div>
    <div>
        ЗАКАЗЧИК _________________________
    </div>
</footer>
<main>
    <div>
        <h1 class="text-center">Договор подряда № {{$contract->number}}</h1>
        <div class="block">
            <div>
                г. {{$settings['city']}}
            </div>
            <div class="text-right">
                "{{$date['day']}}" {{$date['month']}} {{$date['year']}} г.
            </div>
        </div>

        <div class="block bordered">
            <div class="bold" style="width: 100px">ЗАКАЗЧИК</div>
            <div>
                {{$customer->full_name}}, в
                лице {{strtolower($customer->director_title)}} {{$customer->director_for_contract}},
                действующего на основании {{$customer->legal_statement}}
            </div>
        </div>

        <div class="block bordered">
            <div class="bold" style="width: 100px">ПОДРЯДЧИК</div>
            <div>
                {{$settings['legal_full_name']}}, действующий на основании {{$settings['legal_settings']}}
                {{$settings['orgn']}}
            </div>
        </div>

        <div>совместно именуемые "Стороны", заключили настоящий Договор о нижеследующем:</div>

        <h1 class="text-center">ТЕРМИНЫ И ОПРЕДЕЛЕНИЯ</h1>

        <div class="block bordered">
            <div class="bold" style="width: 150px">3D-моделирование</div>
            <div>создание трехмерного визуального объекта (макета 3D-модели) при помощи специализированного программного
                обеспечения.
            </div>
        </div>
        <div class="block bordered">
            <div class="bold" style="width: 150px">3D-модель</div>
            <div>объемное цифровое изображение объекта, созданное при помощи специального программного обеспечения для
                3D моделирования.
            </div>
        </div>
        <div class="block bordered">
            <div class="bold" style="width: 150px">3D-печать</div>
            <div>процесс создания цельных трехмерных объектов на основе цифровой модели.</div>
        </div>
        <div class="block bordered">
            <div class="bold" style="width: 150px">3D-принтер</div>
            <div>оборудование, с помощью которого осуществляется 3D-печать объекта на основе 3D модели.</div>
        </div>

        @foreach($template as $i=>$block)
            <h1 class="text-center">{{$i+1}}. {{$block['title']}}</h1>
            @if(isset($block['lines']))
                @foreach($block['lines'] as $j=>$line)
                    <div class="block @if(isset($line['children'])) bold @endif">
                        <div style="width: 50px">{{$i+1}}.{{$j+1}}.</div>
                        <div style="width: calc(100% - 50px)">{!! $line["text"] !!}</div>
                    </div>
                    @if(isset($line['children']))
                        @foreach($line['children'] as $k=>$childLine)
                            <div class="block">
                                <div style="width: 50px">{{$i+1}}.{{$j+1}}. {{$k+1}}.</div>
                                <div style="width: calc(100% - 50px)">{!! $childLine["text"] !!}</div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        <div class="block">
            <div style="width: 50%">ПОДРЯДЧИК</div>
            <div style="width: 50%">ЗАКАЗЧИК</div>
        </div>
        <div class="block">
            <div style="width: 50%">
                {{$settings['legal_name']}}<br/>
                ОГРНИП {{$settings['ogrn']}}<br/>
                ИНН {{$settings['inn']}}<br/>
                Адрес: {{$settings['address']}}<br/>
                Банковские реквизиты:<br/>
                Р/с {{$settings['rs']}}<br/>
                В {{$settings['bank']}}<br/>
                К/с {{$settings['ks']}}<br/>
                БИК {{$settings['bik']}}<br/>
                Телефон {{$settings['phone']}}<br/>
            </div>
            <div style="width: 50%">
                {{$customer->full_name}}<br/>
                {{$customer->entity_type==='company'?'ОГРН':'ОГРНИП'}} {{$customer->ogrn}}<br/>
                ИНН {{$customer->inn}}<br/>
                @if($customer->entity_type==='company')
                    КПП: {{$customer->kpp}}<br/>
                @endif
                Адрес: {{$customer->address}}<br/>
                Банковские реквизиты:<br/>
                Р/с {{$customer->rs}}<br/>
                В {{$customer->bank}}<br/>
                К/с {{$customer->ks}}<br/>
                БИК {{$customer->bik}}<br/>
                Телефон {{$customer->phone}}<br/>
            </div>
        </div>
    </div>
</main>
</body>
</html>
