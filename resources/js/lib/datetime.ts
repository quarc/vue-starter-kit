import { format, type Locale } from 'date-fns';
import { formatInTimeZone } from 'date-fns-tz';
import { enUS, es, uk } from 'date-fns/locale';

// Map locale codes to date-fns locales
const localeMap: Record<string, Locale> = {
    uk: uk,
    'uk-UA': uk,
    en: enUS,
    'en-US': enUS,
    'en-GB': enUS,
    es: es,
    'es-ES': es,
};

// Map PHP date format tokens to date-fns format tokens
const phpToDateFnsMap: Record<string, string> = {
    // Year
    Y: 'yyyy', // 4-digit year
    y: 'yy', // 2-digit year

    // Month
    m: 'MM', // 2-digit month (01-12)
    n: 'M', // Numeric month (1-12)
    F: 'MMMM', // Full month name (with proper declension for Ukrainian)
    M: 'MMM', // Short month name

    // Day
    d: 'dd', // 2-digit day (01-31)
    j: 'd', // Day without leading zero (1-31)
    S: '', // English ordinal suffix (st, nd, rd, th) - not directly supported

    // Time
    H: 'HH', // 24-hour format with leading zero (00-23)
    G: 'H', // 24-hour format without leading zero (0-23)
    h: 'hh', // 12-hour format with leading zero (01-12)
    g: 'h', // 12-hour format without leading zero (1-12)
    i: 'mm', // Minutes with leading zero (00-59)
    s: 'ss', // Seconds with leading zero (00-59)
    A: 'a', // AM/PM uppercase
    a: 'a', // am/pm lowercase

    // Day of week
    l: 'EEEE', // Full weekday name
    D: 'EEE', // Short weekday name
    w: 'i', // Numeric day of week (0-6, Sunday = 0)
    N: 'i', // ISO-8601 numeric day of week (1-7, Monday = 1)
};

/**
 * Convert PHP date format string to date-fns format string
 */
function convertPhpFormatToDateFns(phpFormat: string): string {
    let dateFnsFormat = '';
    let i = 0;
    let literalStart = -1;

    // Helper to flush literal characters
    const flushLiteral = () => {
        if (literalStart >= 0 && literalStart < i) {
            const literal = phpFormat.substring(literalStart, i);
            if (literal.length > 0) {
                // Escape literal characters in date-fns format
                dateFnsFormat += `'${literal.replace(/'/g, "''")}'`;
            }
            literalStart = -1;
        }
    };

    while (i < phpFormat.length) {
        const char = phpFormat[i];
        const nextChar = i + 1 < phpFormat.length ? phpFormat[i + 1] : '';
        const twoChar = char + nextChar;

        // Check for 2-character tokens first (like 'MM', 'dd', etc.)
        if (phpToDateFnsMap[twoChar] !== undefined) {
            flushLiteral();
            dateFnsFormat += phpToDateFnsMap[twoChar];
            i += 2;
        } else if (phpToDateFnsMap[char] !== undefined) {
            flushLiteral();
            dateFnsFormat += phpToDateFnsMap[char];
            i += 1;
        } else {
            // Start or continue literal sequence
            if (literalStart === -1) {
                literalStart = i;
            }
            i += 1;
        }
    }

    // Flush any remaining literal
    flushLiteral();

    return dateFnsFormat;
}

/**
 * Format date using date-fns with proper localization and timezone support
 *
 * @param date - Date to format (Date, string, or number)
 * @param formatString - PHP-style format string (e.g., 'Y-m-d', 'j F Y')
 * @param locale - Locale code (e.g., 'uk', 'en-US', 'es')
 * @param timeZone - Optional timezone (e.g., 'Europe/Kyiv')
 * @returns Formatted date string
 */
export function formatDate(
    date: Date | string | number,
    formatString: string,
    locale: string = 'en-US',
    timeZone?: string,
): string {
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    // Get date-fns locale
    const dateFnsLocale =
        localeMap[locale] || localeMap[locale.split('-')[0]] || enUS;

    // Convert PHP format to date-fns format
    const dateFnsFormat = convertPhpFormatToDateFns(formatString);

    try {
        // If timezone is specified, use date-fns-tz
        if (timeZone) {
            return formatInTimeZone(d, timeZone, dateFnsFormat, {
                locale: dateFnsLocale,
            });
        }

        // Otherwise use regular format
        return format(d, dateFnsFormat, { locale: dateFnsLocale });
    } catch (error) {
        console.warn('Error formatting date:', error);
        return '';
    }
}
