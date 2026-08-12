import type { Metadata } from 'next';
import { notFound, redirect } from 'next/navigation';
import VueInertiaBridge from '@next/components/vue-inertia-bridge';
import { getInertiaPage } from '@next/lib/inertia';

type RouteProps = {
    params: Promise<{ path?: string[] }>;
    searchParams: Promise<Record<string, string | string[] | undefined>>;
};

const titles: Record<string, string> = {
    Welcome: 'Dịch vụ địa phương uy tín',
    'services/Index': 'Dịch vụ',
    'services/Show': 'Chi tiết dịch vụ',
    'categories/Index': 'Danh mục dịch vụ',
    'search/Index': 'Tìm kiếm',
    'ai-planner/Index': 'AI Planner',
    'about/Index': 'Về chúng tôi',
    'contact/Index': 'Liên hệ',
    'policy/Index': 'Chính sách',
    'auth/Login': 'Đăng nhập',
    'auth/Register': 'Đăng ký',
    'customer/Dashboard': 'Tổng quan khách hàng',
    'provider/Dashboard': 'Tổng quan nhà cung cấp',
    'admin/Dashboard': 'Tổng quan quản trị',
    'chat/Index': 'Tin nhắn',
};

function routeUrl(
    path: string[] | undefined,
    searchParams: Record<string, string | string[] | undefined>,
): string {
    const pathname = path?.length ? `/${path.join('/')}` : '/';
    const query = new URLSearchParams();

    for (const [key, value] of Object.entries(searchParams)) {
        if (Array.isArray(value)) value.forEach((item) => query.append(key, item));
        else if (value !== undefined) query.set(key, value);
    }

    const serialized = query.toString();
    return serialized ? `${pathname}?${serialized}` : pathname;
}

async function load(props: RouteProps) {
    const [{ path }, searchParams] = await Promise.all([
        props.params,
        props.searchParams,
    ]);
    return getInertiaPage(routeUrl(path, searchParams));
}

export async function generateMetadata(props: RouteProps): Promise<Metadata> {
    try {
        const result = await load(props);
        if (result.kind !== 'page') return {};

        const service = result.page.props.service as
            | { title?: string; description?: string; images?: string[] }
            | undefined;
        const pageTitle =
            service?.title ??
            titles[result.page.component] ??
            result.page.component.split('/').at(-1) ??
            'Dalat Services';
        const description =
            service?.description ??
            'Khám phá và đặt dịch vụ địa phương uy tín tại Đà Lạt.';

        return {
            title: pageTitle,
            description,
            alternates: { canonical: result.page.url },
            openGraph: {
                title: pageTitle,
                description,
                url: result.page.url,
                images: service?.images?.[0] ? [service.images[0]] : undefined,
            },
        };
    } catch {
        return {};
    }
}

export const dynamic = 'force-dynamic';

export default async function MigratedPage(props: RouteProps) {
    const result = await load(props);

    if (result.kind === 'not-found') notFound();
    if (result.kind === 'redirect') redirect(result.location);

    return <VueInertiaBridge initialPage={result.page} />;
}
