
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

const firebaseConfig = {
    apiKey: "AIzaSyASbdtKoxsgRTkvos2oNn4PMAIYidlXYz0",
    authDomain: "my-shop-a2caf.firebaseapp.com",
    projectId: "my-shop-a2caf",
    storageBucket: "my-shop-a2caf.firebasestorage.app",
    messagingSenderId: "588832126711",
    appId: "1:588832126711:web:eed4fdf0968ee1d2c345f2"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/logo.png', // Fallback icon path, ensure this exists or use valid one
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
