export const orderStatuses = [
    {value: 'new', text: 'Новый'},
    {value: 'modeling', text: 'На моделировании'},
    {value: 'printing', text: 'В печати'},
    {value: 'processing', text: 'На упаковке/Пост. обработке'},
    {value: 'moving', text: 'Перемещение на ПВЗ'},
    {value: 'moving_tk', text: 'Перемещение ТК'},
    {value: 'shipping', text: 'Готов к отгрузке'},
    {value: 'completed', text: 'Отгружен'},
    {value: 'canceled', text: 'Отменён'},
];

export const orderStatusLabel = (key) => {
    return orderStatuses.find(x => x.value === key)?.text;
}

export const paymentStatuses = [
    {value:'not_paid',text:'Не оплачен'},
    {value:'part_paid',text:'Частично оплачен'},
    {value:'full_paid',text:'Полностью оплачен'},
];

export const paymentStatusLabel = (key) => {
    return paymentStatuses.find(x => x.value === key)?.text;
}