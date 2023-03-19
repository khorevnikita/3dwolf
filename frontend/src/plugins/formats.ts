export const formatPrice = (price: number) => {
    return price.toLocaleString().replace(/,/g, ' ') + ' ₽';
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