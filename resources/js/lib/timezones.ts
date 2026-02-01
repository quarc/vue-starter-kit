export interface Timezone {
    value: string;
    label: string;
    offset: number;
    region: string;
    city: string;
}

/**
 * Get timezone offset in seconds for a given timezone at a specific date
 */
function getTimezoneOffset(date: Date, timeZone: string): number {
    // Create two dates representing the same moment in UTC and in the target timezone
    // Format them as ISO strings to get the UTC representation
    const utcDate = new Date(date.toLocaleString('en-US', { timeZone: 'UTC' }));
    const tzDate = new Date(date.toLocaleString('en-US', { timeZone }));

    // Calculate the difference in milliseconds, then convert to seconds
    // The offset is the difference between how the same moment is represented
    return (tzDate.getTime() - utcDate.getTime()) / 1000;
}

/**
 * Get all available timezones using Intl API
 */
export function getTimezones(): Timezone[] {
    const timezones: Timezone[] = [];
    const now = new Date();

    // Use Intl.supportedValuesOf to get all timezones (ES2022)
    // Fallback for older browsers
    let tzNames: string[];
    try {
        tzNames = Intl.supportedValuesOf('timeZone');
    } catch {
        // Fallback: use a common set of timezones if Intl.supportedValuesOf is not available
        tzNames = ['UTC'];
    }

    tzNames.forEach((tzName) => {
        try {
            const offset = getTimezoneOffset(now, tzName);
            const offsetHours = offset / 3600;
            const hours = Math.floor(offsetHours);
            const minutes = Math.abs(Math.floor((offset % 3600) / 60));
            const sign = hours >= 0 ? '+' : '-';
            const offsetFormatted = `${sign}${Math.abs(hours).toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

            // Extract region and city
            const parts = tzName.split('/', 2);
            const region = parts[0];
            const city = parts[1] ? parts[1].replace(/_/g, ' ') : tzName;

            timezones.push({
                value: tzName,
                label: `(UTC${offsetFormatted}) ${tzName.replace(/_/g, ' ')}`,
                offset,
                region,
                city,
            });
        } catch (e) {
            // Skip invalid timezones
            console.warn(`Invalid timezone: ${tzName}`, e);
        }
    });

    // Sort by offset, then by label
    timezones.sort((a, b) => {
        if (a.offset === b.offset) {
            return a.label.localeCompare(b.label);
        }
        return a.offset - b.offset;
    });

    return timezones;
}
