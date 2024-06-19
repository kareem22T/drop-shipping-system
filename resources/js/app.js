import './bootstrap';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Your web app's Firebase configuration
const firebaseConfig = {

    apiKey: "AIzaSyBNfeZK4Ge3qQvAXX6eJXAqrRcTEwyKrSA",

    authDomain: "drop-shipping-9e995.firebaseapp.com",

    projectId: "drop-shipping-9e995",

    storageBucket: "drop-shipping-9e995.appspot.com",

    messagingSenderId: "310963562489",

    appId: "1:310963562489:web:3fef8d8d703beab302bf28",

    measurementId: "G-8B3554ZX5Y"

  };

// Initialize Firebase
const firebaseApp = initializeApp(firebaseConfig);

// Initialize Firebase Cloud Messaging and get a reference to the service
const messaging = getMessaging(firebaseApp);
// Request permission and get token
Notification.requestPermission()
    .then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            // Get the registration token
            return getToken(messaging, { vapidKey: 'YOUR_PUBLIC_VAPID_KEY' });
        } else {
            console.log('Unable to get permission to notify.');
        }
    })
    .then((currentToken) => {
        if (currentToken) {
            console.log('FCM Token:', currentToken);
            // You can now send this token to your server and subscribe to topics or save it for individual use
        } else {
            console.log('No registration token available. Request permission to generate one.');
        }
    })
    .catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });

// Handle incoming messages
onMessage(messaging, (payload) => {
    console.log('Message received. ', payload);
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    if (Notification.permission === 'granted') {
        new Notification(notificationTitle, notificationOptions);
    }
});
