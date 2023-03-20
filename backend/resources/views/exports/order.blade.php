<table>
    <tbody>
    <tr>
        <td colspan="8">
            <img alt="logo" src="{{storage_path('app/public/logo.svg')}}"/>
        </td>
        <td colspan="4">
            <b>3D WOLF</b><br/>
            Печать, моделирование <br/>
            +7 499 133-1423 <br/>
            https://3dwolf.ru <br/>
            co@3dwolf.ru
        </td>
    </tr>
    <tr>
        <td colspan="12">
            <b style="font-size: 24px">Наряд заказ № {{$order->id}}</b>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <b>Заказчик</b>
        </td>
        <td colspan="8"></td>
        <td>
            <b>Дата заказа</b>
        </td>
        <td>{{$order->date?\Carbon\Carbon::parse($order->date)->format("d.m.Y"):'-'}}</td>
    </tr>
    <tr>
        <td></td>
        <td>ФИО</td>
        <td>{{$customer->surname}} {{$customer->name}} {{$customer->father_name}}</td>
        <td colspan="7"></td>
        <td>
            <b>Дедлайн заказа</b>
        </td>
        <td>{{$order->deadline?\Carbon\Carbon::parse($order->deadline)->format("d.m.Y"):''}}</td>
    </tr>
    <tr>
        <td></td>
        <td>Телефон</td>
        <td>{{$order->phone}}</td>
        <td colspan="7"></td>
        <td>
            <b>КАНАЛ</b>
        </td>
        <td>{{$customer->source==='site'?'САЙТ':($customer->source==='avito'?'Авито':'-')}}</td>
    </tr>
    <tr>
        <td></td>
        <td>E-mail</td>
        <td>{{$customer->email}}</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td></td>
        <td>Telegram</td>
        <td>{{$customer->telegram}}</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td></td>
        <td>Адрес доставки</td>
        <td>
            <b>{{$customer->delivery_address}}</b>
        </td>
        <td colspan="9"></td>
    </tr>


    <tr>
        <td colspan="12"></td>
    </tr>

    <tr style="background: gray;font-weight: bold;text-align: center">
        <td>#</td>
        <td colspan="4">Наименование</td>
        <td>Вес детали (гр./мл.)</td>
        <td>Время печати</td>
        <td>Материал</td>
        <td>Цвет / Артикул</td>
        <td>Кол-во в партии</td>
        <td>Цена единицы</td>
        <td>ИТОГО</td>
    </tr>

    @foreach($order->lines as $k=>$line)
        <tr>
            <td>{{$k+1}}</td>
            <td colspan="4">{{$line->name}}</td>
            <td>{{$line->weight}}</td>
            <td>{{$line->print_duration}}</td>
            <td>{{$line->part->material->name}}</td>
            <td>{{$line->part->color}}</td>
            <td>{{$line->count}}</td>
            <td>{{$line->price}}</td>
            <td>{{$line->total_amount}}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="10"></td>
        <td><b>ИТОГО</b></td>
        <td><b>{{$order->amount}}</b></td>
    </tr>
    <tr>
        <td colspan="12"></td>
    </tr>
    <tr>
        <td colspan="12"></td>
    </tr>
    <tr>
        <td>Исполнитель</td>
        <td colspan="3">______________________</td>
    </tr>
    <tr>
        <td>Дата</td>
        <td colspan="3">______________________</td>
    </tr>
    </tbody>
</table>
