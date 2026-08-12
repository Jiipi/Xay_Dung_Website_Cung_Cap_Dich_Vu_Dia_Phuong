import type { Metadata, Viewport } from 'next';
import { Playfair_Display, Plus_Jakarta_Sans } from 'next/font/google';
import type { ReactNode } from 'react';
import { getSiteUrl } from '@next/lib/site-url';
import '../resources/css/app.css';

const plusJakarta = Plus_Jakarta_Sans({
    subsets: ['latin', 'vietnamese'],
    variable: '--font-plus-jakarta',
    display: 'swap',
});

const playfair = Playfair_Display({
    subsets: ['latin', 'vietnamese'],
    variable: '--font-playfair',
    display: 'swap',
});

const siteUrl = getSiteUrl();

export const metadata: Metadata = {
    metadataBase: siteUrl,
    title: {
        default: 'Dalat Services — Dịch vụ địa phương uy tín',
        template: '%s - Dalat Services',
    },
    description:
        'Khám phá và đặt dịch vụ địa phương uy tín tại Đà Lạt với giá minh bạch và đánh giá thật.',
    applicationName: 'Dalat Services',
    icons: {
        icon: [
            { url: '/favicon.ico', sizes: 'any' },
            { url: '/favicon.png', type: 'image/png' },
        ],
        apple: '/apple-touch-icon.png',
    },
    openGraph: {
        type: 'website',
        locale: 'vi_VN',
        siteName: 'Dalat Services',
    },
    twitter: { card: 'summary_large_image' },
};

export const viewport: Viewport = {
    width: 'device-width',
    initialScale: 1,
    colorScheme: 'light dark',
    themeColor: [
        { media: '(prefers-color-scheme: light)', color: '#f8f5f0' },
        { media: '(prefers-color-scheme: dark)', color: '#0f1a14' },
    ],
};

export default function RootLayout({ children }: { children: ReactNode }) {
    return (
        <html
            lang="vi"
            suppressHydrationWarning
            className={`${plusJakarta.variable} ${playfair.variable}`}
        >
            <body>{children}</body>
        </html>
    );
}
