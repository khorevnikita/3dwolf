export const formatPrice = (price: number = 0) => {
    return price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$& ') + ' ₽';
}

export const formatDuration = (seconds: number) => {
    let h = Math.floor(seconds / 3600);
    seconds %= 3600;
    let m = Math.floor(seconds / 60);
    const mString = m < 10 ? `0${m}` : m;
    let s = seconds % 60;
    const sString = s < 10 ? `0${s}` : s;

    return `${h}:${mString}:${sString}`
}