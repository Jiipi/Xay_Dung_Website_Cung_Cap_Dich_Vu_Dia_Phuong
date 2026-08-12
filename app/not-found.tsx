import Link from 'next/link';

export default function NotFound() {
    return (
        <main className="grid min-h-screen place-items-center bg-stone-50 p-6">
            <section className="max-w-lg text-center">
                <p className="text-7xl font-black text-emerald-700">404</p>
                <h1 className="mt-4 text-3xl font-bold text-stone-900">
                    Không tìm thấy trang
                </h1>
                <p className="mt-3 text-stone-600">
                    Đường dẫn bạn truy cập không tồn tại hoặc đã được di chuyển.
                </p>
                <Link
                    href="/"
                    className="mt-7 inline-flex rounded-full bg-emerald-700 px-6 py-3 text-sm font-semibold text-white"
                >
                    Về trang chủ
                </Link>
            </section>
        </main>
    );
}
