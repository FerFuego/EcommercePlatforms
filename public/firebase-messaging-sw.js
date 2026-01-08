importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyDAe5zENBwfFxZmxpGIRs9zB2qv78gavuw",
    authDomain: "cocinarte-bdf3e.firebaseapp.com",
    projectId: "cocinarte-bdf3e",
    storageBucket: "cocinarte-bdf3e.firebasestorage.app",
    messagingSenderId: "672451281624",
    appId: "1:672451281624:web:b939bfd945b5a193cd4dad"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {

    const notificationTitle = payload.notification?.title || 'Nueva Notificación';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/favicon.ico',
        data: payload.data
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
