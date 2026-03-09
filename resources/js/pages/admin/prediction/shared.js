export const PM10_SERIES = [
    30, 31, 33, 30, 20, 15, 49, 34, 28, 36, 25, 18, 42, 17, 35, 44, 29, 33, 33, 40, 45, 26, 20, 17,
];

export const PM25_SERIES = [
    2, 9.5, 9, 8.5, 14, 5, 14, 3, 2.5, 12.5, 12.5, 2, 12, 2, 6, 14.5, 2.5, 1, 3, 3, 14, 12.5, 12.5, 3,
];

const indicatorFromPm25 = (pm25) => {
    if (pm25 <= 15.5) return 'Baik';
    if (pm25 <= 55.4) return 'Sedang';
    return 'Tidak Sehat';
};

export const BASE_ROWS = PM10_SERIES.map((pm10, hour) => {
    const pm25 = PM25_SERIES[hour];
    return {
        time: `${String(hour).padStart(2, '0')}.00`,
        pm10,
        pm25,
        indicator: indicatorFromPm25(pm25),
    };
});

export const repeatRowsByDay = (days) => {
    const rows = [];
    for (let day = 0; day < days; day += 1) {
        BASE_ROWS.forEach((row) => {
            rows.push({
                ...row,
                time: day === 0 ? row.time : `H${day + 1} ${row.time}`,
            });
        });
    }
    return rows;
};

export const DATA_BY_RANGE = {
    '24 Jam': BASE_ROWS,
    '7 Hari': repeatRowsByDay(7),
    '30 Hari': repeatRowsByDay(30),
};

export const formatIdDate = (isoDate) => {
    try {
        const date = new Date(`${isoDate}T00:00:00`);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return isoDate;
    }
};

export const stats = (values) => {
    if (!values.length) return { avg: '0.0', max: 0, min: 0 };
    return {
        avg: (values.reduce((a, b) => a + b, 0) / values.length).toFixed(1),
        max: Math.max(...values),
        min: Math.min(...values),
    };
};
