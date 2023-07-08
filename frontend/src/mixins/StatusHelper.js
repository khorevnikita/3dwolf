export const orderStatuses = [
    {value: 'new', text: 'Новый'},
    {value: 'printing', text: 'В печати'},
    {value: 'moving', text: 'Перемещение на ПВЗ'},
    {value: 'moving_tk', text: 'Перемещение ТК'},
    {value: 'shipping', text: 'Готов к отгрузке'},
    {value: 'completed', text: 'Отгружен'},
    {value: 'canceled', text: 'Отменён'},
];

export const orderStatusLabel = (key) => {
    return orderStatuses.find(x => x.value === key)?.text;
}