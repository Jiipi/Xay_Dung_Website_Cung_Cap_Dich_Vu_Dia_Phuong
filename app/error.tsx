'use client';

import { useEffect } from 'react';

export default function ErrorPage({
    error,
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    useEffect(() => {
        console.error(error);
    }, [error]);

    return (
        <main className="grid min-h-screen place-items-center bg-stone-50 p-6">
            <section className="max-w-lg rounded-3xl border border-red-200 bg-white p-8 text-center shadow-sm">
                <p className="text-sm font-bold tracking-widest text-red-600 uppercase">
                    Lỗi kết nối
                </p>
                <h1 className="mt-3 text-3xl font-bold text-stone-900">
                    Không thể tải trang
                </h1>
                <p className="mt-3 text-sm leading-6 text-stone-600">
                    Frontend Next.js chưa nhận được phản hồi hợp lệ từ backend
                    Laravel. Vui lòng thử lại hoặc kiểm tra cấu hình
                    LARAVEL_API_URL.
                </p>
                <button
                    type="button"
                    onClick={reset}
                    className="mt-6 rounded-full bg-emerald-700 px-6 py-3 text-sm font-semibold text-white"
                >
                    Thử lại
                </button>
            </section>
        </main>
    );
}
