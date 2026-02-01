import { formatDate } from './datetime';

export interface DateFormat {
    value: string;
    example: string;
}

export interface TimeFormat {
    value: string;
    example: string;
}

/**
 * Get common date formats with examples
 */
export function getDateFormats(locale: string = 'en'): DateFormat[] {
    const now = new Date();

    const formats = [
        { value: 'Y-m-d' },
        { value: 'd/m/Y' },
        { value: 'm/d/Y' },
        { value: 'd-m-Y' },
        { value: 'd.m.Y' },
        { value: 'j F Y' },
        { value: 'F j, Y' },
        { value: 'D, M j, Y' },
        { value: 'l, F j, Y' },
    ];

    return formats.map((format) => ({
        value: format.value,
        example: formatDate(now, format.value, locale),
    }));
}

/**
 * Get common time formats with examples
 */
export function getTimeFormats(locale: string = 'en'): TimeFormat[] {
    const now = new Date();

    const formats = [
        { value: 'H:i' },
        { value: 'H:i:s' },
        { value: 'g:i A' },
        { value: 'g:i:s A' },
    ];

    return formats.map((format) => ({
        value: format.value,
        example: formatDate(now, format.value, locale),
    }));
}
