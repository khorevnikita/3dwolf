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

    .title{
        font-size: 11px;
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
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr>
        <td colspan="24" class="text-center">
            <b style="font-size: 24px">Наряд заказ № {{$order->id}}</b>
        </td>
    </tr>
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr><td colspan="24">&nbsp;</td></tr>
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
            <b>{{$customer->delivery_address}}</b>
        </td>
        <td colspan="18"></td>
    </tr>
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr class="text-center bordered" style="background: gray;font-weight: bold;">
        <td class="text-right title">#</td>
        <td colspan="8" class="title">Наименование</td>
        <td colspan="2" class="title">Вес детали (гр./мл.)</td>
        <td colspan="2" class="title">Время печати</td>
        <td colspan="3" class="title">Материал</td>
        <td colspan="2" class="title">Цвет / Артикул</td>
        <td colspan="2" class="title">Кол-во в партии</td>
        <td colspan="1" class="title">Цена единицы</td>
        <td colspan="3" class="title">ИТОГО</td>
    </tr>

    @foreach($order->lines as $k=>$line)
        <tr class="bordered">
            <td class="text-right">{{$k+1}}</td>
            <td colspan="8">{{$line->name}}</td>
            <td colspan="2">{{$line->weight}}</td>
            <td colspan="2">{{$line->print_duration}}</td>
            <td colspan="3">{{$line->part->material->name}}</td>
            <td colspan="2">{{$line->part->color}}</td>
            <td colspan="2">{{$line->count}}</td>
            <td colspan="1">{{$line->price}}</td>
            <td colspan="3">{{$line->total_amount}}</td>
        </tr>
    @endforeach
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr>
        <td colspan="20"></td>
        <td><b>ИТОГО</b></td>
        <td><b>{{$order->amount}}</b></td>
    </tr>
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr>
        <td colspan="2">Исполнитель</td>
        <td colspan="6">______________________</td>
    </tr>
    <tr><td colspan="24">&nbsp;</td></tr>
    <tr>
        <td colspan="2">Дата</td>
        <td colspan="6">______________________</td>
    </tr>
    </tbody>
</table>
