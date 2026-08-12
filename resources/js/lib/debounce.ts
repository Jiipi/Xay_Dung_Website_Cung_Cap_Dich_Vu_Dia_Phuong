export default function debounce<TArgs extends unknown[]>(
    callback: (...args: TArgs) => void,
    delayMs: number,
): (...args: TArgs) => void {
    let timeoutId: ReturnType<typeof setTimeout> | undefined;

    return (...args: TArgs) => {
        if (timeoutId !== undefined) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => callback(...args), delayMs);
    };
}
