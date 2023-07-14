export const formatPrice = (price: number = 0) => {
    return Number(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$& ') + ' ₽';
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

export const formatWeight = (bytes: number, decimals = 2) => {
    if (!+bytes) return '0 Bytes'

    const k = 1024
    const dm = decimals < 0 ? 0 : decimals
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
}

export const formatKg = (gramms: number) => {
    const kg = Math.round(gramms / 1000 * 100) / 100;
    return kg.toFixed(2);
}