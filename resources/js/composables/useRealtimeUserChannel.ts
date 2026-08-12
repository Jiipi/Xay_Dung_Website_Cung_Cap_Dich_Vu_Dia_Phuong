import { router, usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

type RealtimeNotification = {
    id: number;
    title: string;
    body: string;
    type: string;
    read: boolean;
    date?: string;
};

type RealtimePageProps = {
    auth?: { user?: { id?: number | string } };
    unreadNotifications?: number;
    pendingBookingsCount?: number;
};

type NotificationCreatedEvent = {
    unreadNotifications?: number;
    notification?: RealtimeNotification;
};

type NotificationReadEvent = {
    unreadNotifications?: number;
    notificationId?: number;
};

type BookingUpdatedEvent = {
    pendingBookingsCount?: number;
};

const unreadNotifications = ref(0);
const pendingBookingsCount = ref(0);
const recentNotifications = ref<RealtimeNotification[]>([]);
let listeners = 0;
let channelName: string | null = null;

function propsFrom(page: ReturnType<typeof usePage>): RealtimePageProps {
    return page.props as RealtimePageProps;
}

function syncFromPageProps(page: ReturnType<typeof usePage>) {
    const props = propsFrom(page);
    unreadNotifications.value = Number(
        props.unreadNotifications ?? unreadNotifications.value,
    );
    pendingBookingsCount.value = Number(
        props.pendingBookingsCount ?? pendingBookingsCount.value,
    );
}

function refreshVisibleDashboard() {
    router.reload({
        only: [
            'stats',
            'bookings',
            'recentBookings',
            'notifications',
            'unreadCount',
            'pendingBookingsCount',
            'unreadNotifications',
        ],
    });
}

function subscribe(page: ReturnType<typeof usePage>) {
    const userId = propsFrom(page).auth?.user?.id;
    const echo = window.Echo;

    if (!userId || !echo || channelName) return;

    channelName = `App.Models.User.${userId}`;
    echo.private(channelName)
        .listen('NotificationCreated', (event: NotificationCreatedEvent) => {
            unreadNotifications.value = Number(
                event.unreadNotifications ?? unreadNotifications.value + 1,
            );
            if (event.notification) {
                recentNotifications.value = [
                    event.notification,
                    ...recentNotifications.value.filter(
                        (notification) =>
                            notification.id !== event.notification?.id,
                    ),
                ].slice(0, 5);
            }
        })
        .listen(
            'NotificationReadStateUpdated',
            (event: NotificationReadEvent) => {
                unreadNotifications.value = Number(
                    event.unreadNotifications ?? unreadNotifications.value,
                );
                if (event.notificationId) {
                    recentNotifications.value = recentNotifications.value.map(
                        (notification) =>
                            notification.id === event.notificationId
                                ? { ...notification, read: true }
                                : notification,
                    );
                } else {
                    recentNotifications.value = recentNotifications.value.map(
                        (notification) => ({ ...notification, read: true }),
                    );
                }
            },
        )
        .listen('BookingUpdated', (event: BookingUpdatedEvent) => {
            pendingBookingsCount.value = Number(
                event.pendingBookingsCount ?? pendingBookingsCount.value,
            );
            refreshVisibleDashboard();
        });
}

function unsubscribe() {
    const echo = window.Echo;
    if (listeners > 0 || !echo || !channelName) return;

    echo.leave(channelName);
    channelName = null;
}

export function useRealtimeUserChannel() {
    const page = usePage();
    syncFromPageProps(page);

    onMounted(() => {
        listeners += 1;
        subscribe(page);
    });

    onUnmounted(() => {
        listeners = Math.max(0, listeners - 1);
        unsubscribe();
    });

    function setRecentNotifications(notifications: RealtimeNotification[]) {
        recentNotifications.value = notifications;
    }

    return {
        unreadNotifications,
        pendingBookingsCount,
        recentNotifications,
        setRecentNotifications,
    };
}
