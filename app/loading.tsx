export default function Loading() {
    return (
        <main className="grid min-h-screen place-items-center bg-stone-50">
            <div
                className="size-10 animate-spin rounded-full border-4 border-emerald-700 border-t-transparent"
                role="status"
                aria-label="Đang tải"
            />
        </main>
    );
}
