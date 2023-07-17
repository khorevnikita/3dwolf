@php
    function secondsToTime($sec){
        if(!$sec) return "";
        $h = floor($sec / 3600);
        $sec = $sec % 3600;
        $m = floor($sec/60);
        $mString = $m<10?"0$m":$m;
        $s = $sec % 60;
        $sString = $s<10?"0$s":$s;

        return "$h:$mString:$sString";
    }
@endphp
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
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    table {
        width: 100%;
    }

    .item-lines {
        font-size: 11px;
    }


    .title {
        font-size: 10px;
    }

    .bordered {
        border-top: 1px solid;
    }

    .bordered td {
        border-left: 1px solid;
        border-bottom: 1px solid;

    }

    .bordered td:last-of-type {
        border-right: 1px solid;
    }

    .cell-short {
        width: 30px;
    }

</style>

<table>
    <tbody>
    <tr>
        <td colspan="16">
            <img alt="logo" src="{{storage_path('app/public/logo.png')}}"/>
        </td>
        <td class="text-right" colspan="8">
            <b>3D WOLF</b><br/>
            Печать, моделирование <br/>
            +7 499 133-1423 <br/>
            https://3dwolf.ru <br/>
            co@3dwolf.ru
        </td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="24" class="text-center">
            <b style="font-size: 24px">Наряд заказ № {{$order->id}}</b>
        </td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td></td>
        <td>
            <b>Заказчик</b>
        </td>
        <td colspan="15"></td>
        <td colspan="4">
            <b>Дата заказа</b>
        </td>
        <td>{{$order->date?\Carbon\Carbon::parse($order->date)->format("d.m.Y"):'-'}}</td>
    </tr>

    <tr>
        <td></td>
        <td>ФИО</td>
        <td colspan="8">{{$customer->surname}} {{$customer->name}} {{$customer->father_name}}</td>
        <td colspan="7"></td>
        <td colspan="4">
            <b>Дедлайн заказа</b>
        </td>
        <td>{{$order->deadline?\Carbon\Carbon::parse($order->deadline)->format("d.m.Y"):''}}</td>
    </tr>
    <tr>
        <td></td>
        <td>Телефон</td>
        <td colspan="11">{{$order->phone}}</td>
        <td colspan="4"></td>
        <td colspan="4">
            <b>КАНАЛ</b>
        </td>
        <td>{{$customer->source==='site'?'САЙТ':($customer->source==='avito'?'Авито':'-')}}</td>
    </tr>
    <tr>
        <td></td>
        <td>E-mail</td>
        <td colspan="4">{{$customer->email}}</td>
        <td colspan="16"></td>
    </tr>
    <tr>
        <td></td>
        <td>Telegram</td>
        <td>{{$customer->telegram}}</td>
        <td colspan="18"></td>
    </tr>
    <tr>
        <td></td>
        <td>Адрес доставки</td>
        <td>
            <b>{{$order->delivery_address}}</b>
        </td>
        <td colspan="18"></td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    </tbody>
</table>
<table>
    <tbody>
    <tr class="text-center bordered" style="background: #d7d7d7;font-weight: bold;">
        <td class="title">#</td>
        <td class="title">Наименование</td>
        <td class="title">Вес детали (гр./мл.)</td>
        <td class="title">Время печати</td>
        <td class="title">Материал</td>
        <td class="title">Цвет / Артикул</td>
        <td class="title">Кол-во</td>
        <td class="title">Цена единицы, ₽</td>
        <td class="title" style="min-width: 65px">ИТОГО ,₽</td>
    </tr>

    @foreach($order->lines as $k=>$line)
        <tr class="bordered">
            <td class="text-center">{{$k+1}}</td>
            <td>
                {{$line->name}}
                @if($line->filling)
                    ({{$line->filling}}%)
                @endif
            </td>
            <td class="text-center">{{$line->weight?:""}}</td>
            <td class="text-center">{{secondsToTime($line->print_duration)}}</td>
            <td class="text-center">{{$line->part?->material?->name}}</td>
            <td>
                @if($line->part)
                    {{$line->part->color}} ({{$line->part->prod_number}})
                @endif
            </td>
            <td class="text-center" colspan="1">{{$line->count}}</td>
            <td class="text-center">{{number_format($line->price, 2, ',', ' ')}}</td>
            <td class="text-center">{{number_format($line->total_amount, 2, ',', ' ')}}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    </tbody>
</table>
<table class="item-lines">
    <tbody>
    <tr>
        <td colspan="20"></td>
        <td><b>ИТОГО</b></td>
        <td><b>{{number_format($order->amount, 2, ',', ' ')}} ₽</b></td>
    </tr>
    @if($order->discount)
        <tr>
            <td colspan="20"></td>
            <td><b>Скидка</b></td>
            <td><b>{{$order->discount}} %</b></td>
        </tr>
        <tr>
            <td colspan="20"></td>
            <td><b>ИТОГО с учётом скидки</b></td>
            <td><b>{{number_format($order->amount_after_discount, 2, ',', ' ')}} ₽</b></td>
        </tr>
    @endif
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">Исполнитель</td>
        <td colspan="6">______________________</td>
    </tr>
    <tr>
        <td colspan="24">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">Дата</td>
        <td colspan="6">______________________</td>
    </tr>
    </tbody>
</table>
