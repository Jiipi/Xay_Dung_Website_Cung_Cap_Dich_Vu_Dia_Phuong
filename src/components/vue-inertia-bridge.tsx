'use client';

import type { Page } from '@inertiajs/core';
import { useEffect, useId, useRef, useState } from 'react';

type Props = { initialPage: Page };

async function refreshSession(initialPage: Page): Promise<Page> {
    try {
        const response = await fetch(window.location.href, {
            method: 'GET',
            cache: 'no-store',
            credentials: 'same-origin',
            headers: {
                Accept: 'text/html, application/xhtml+xml',
                'X-Inertia': 'true',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (
            response.ok &&
            (response.headers.get('content-type') ?? '').includes(
                'application/json',
            )
        ) {
            return (await response.json()) as Page;
        }
    } catch {
        // The server-rendered payload remains usable when the CSRF bootstrap
        // request is temporarily unavailable.
    }

    return initialPage;
}

export default function VueInertiaBridge({ initialPage }: Props) {
    const reactId = useId().replaceAll(':', '');
    const mountId = `inertia-vue-${reactId}`;
    const mounted = useRef(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (mounted.current) return;
        mounted.current = true;
        let disposed = false;
        let disposeVue: (() => void) | undefined;

        void (async () => {
            try {
                const page = await refreshSession(initialPage);
                if (disposed) return;
                const { mountInertiaVueApp } = await import(
                    '../../resources/js/next-bridge'
                );
                if (disposed) return;
                disposeVue = await mountInertiaVueApp(mountId, page);
            } catch (reason) {
                if (!disposed) {
                    setError(
                        reason instanceof Error
                            ? reason.message
                            : 'Không thể khởi tạo giao diện.',
                    );
                }
            }
        })();

        return () => {
            disposed = true;
            disposeVue?.();
            mounted.current = false;
        };
    }, [initialPage, mountId]);

    if (error) {
        return (
            <main className="grid min-h-screen place-items-center bg-stone-50 p-6">
                <section className="max-w-lg rounded-3xl border border-red-200 bg-white p-8 text-center shadow-sm">
                    <h1 className="text-2xl font-bold text-stone-900">
                        Không thể tải giao diện
                    </h1>
                    <p className="mt-3 text-sm text-stone-600">{error}</p>
                    <button
                        type="button"
                        onClick={() => window.location.reload()}
                        className="mt-6 rounded-full bg-emerald-700 px-5 py-3 text-sm font-semibold text-white"
                    >
                        Thử lại
                    </button>
                </section>
            </main>
        );
    }

    return (
        <div id={mountId}>
            <div className="grid min-h-screen place-items-center bg-stone-50">
                <div
                    className="size-10 animate-spin rounded-full border-4 border-emerald-700 border-t-transparent"
                    role="status"
                    aria-label="Đang tải"
                />
            </div>
        </div>
    );
}
