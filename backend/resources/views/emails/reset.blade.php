<div>
    <h1>Привет, {{$user->name}}</h1>
    <p>Вам сбросили пароль от {{config("app.name")}}!</p>
    <p>Логин для входа: <b>{{$user->email}}</b></p>
    <p>Новый пароль: <b>{{$password}}</b></p>
    <a href="{{url("/")}}">Войти</a>
    <br/>
    @include("emails.footer")
</div>
