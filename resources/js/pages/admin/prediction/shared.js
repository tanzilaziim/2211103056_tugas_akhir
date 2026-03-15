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
    const numbers = (Array.isArray(values) ? values : [])
        .filter((v) => v !== null && v !== undefined && Number.isFinite(Number(v)))
        .map((v) => Number(v));
    if (!numbers.length) return { avg: '0.0', max: 0, min: 0 };

    return {
        avg: (numbers.reduce((a, b) => a + b, 0) / numbers.length).toFixed(1),
        max: Math.max(...numbers),
        min: Math.min(...numbers),
    };
};

