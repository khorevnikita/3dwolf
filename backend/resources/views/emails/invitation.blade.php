<div>
    <h1>Привет, {{$user->name}}</h1>
    <p>Добро пожаловать в {{config("app.name")}}!</p>
    <p>Логин для входа: <b>{{$user->email}}</b></p>
    <p>Пароль: <b>{{$password}}</b></p>
    <a href="{{url("/")}}">Войти</a>
</div>
