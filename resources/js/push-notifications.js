import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: "AIzaSyDAe5zENBwfFxZmxpGIRs9zB2qv78gavuw",
    authDomain: "cocinarte-bdf3e.firebaseapp.com",
    projectId: "cocinarte-bdf3e",
    storageBucket: "cocinarte-bdf3e.firebasestorage.app",
    messagingSenderId: "672451281624",
    appId: "1:672451281624:web:b939bfd945b5a193cd4dad",
    measurementId: "G-DD8VGPBZR9"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Register Service Worker explicitly for better control
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            // Service worker registered
        })
        .catch((err) => {
            console.error('Service Worker registration failed', err);
        });
}

export function requestPermission() {
    if (Notification.permission === 'default') {
        // We'll wait for a user click to trigger the real prompt if needed
        // but for now let's try the regular prompt
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                saveToken();
            } else {
                console.log('Permission not granted:', permission);
            }
        });
    } else if (Notification.permission === 'granted') {
        saveToken();
    }
}

function saveToken() {
    // Note: YOUR_VAPID_KEY should be replaced by the user eventually
    getToken(messaging, { vapidKey: 'BPS0SEu6tpoMGhXuPiudeiL3M6aOiB-Do-prGZPhDPQsCSUoAZ1YwjWM-BGIxmwK2fs_M_2XpSIOrPfikXn2GXE' }).then((currentToken) => {
        if (currentToken) {
            sendTokenToServer(currentToken);
        } else {
            console.log('No registration token available. Request permission to generate one.');
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
}

function sendTokenToServer(token) {
    fetch('/push-tokens', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            token: token,
            device_type: getDeviceType()
        })
    })
        .then(response => response.json())
        .then(data => {
            // Token saved
        })
        .catch(error => console.error('Error saving token:', error));
}

function getDeviceType() {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
        return "tablet";
    }
    if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/i.test(ua)) {
        return "mobile";
    }
    return "web";
}

onMessage(messaging, (payload) => {
    // Extract notification data
    const title = payload.notification?.title || 'Nueva Notificación';
    const body = payload.notification?.body || '';
    const icon = payload.notification?.icon || '/favicon.ico';

    // 1. Show a browser notification via the Service Worker (more reliable)
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then((registration) => {
            registration.showNotification(title, {
                body: body,
                icon: icon,
                data: payload.data
            });
        });
    }

    // 2. Dispatch a custom event for an in-app Toast (AlpineJS)
    window.dispatchEvent(new CustomEvent('push-notification', {
        detail: { title, body, icon, data: payload.data }
    }));
});
