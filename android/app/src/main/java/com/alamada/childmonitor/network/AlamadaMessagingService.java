package com.alamada.childmonitor.network;

import android.content.Context;

/**
 * Firebase Cloud Messaging stub.
 *
 * Firebase was removed because the placeholder google-services.json caused
 * INSTALL_FAILED_INVALID_APK on many devices.
 *
 * To re-enable push notifications:
 * 1. Create a real Firebase project at https://console.firebase.google.com
 * 2. Add an Android app with package name: com.alamada.childmonitor
 * 3. Download google-services.json → place in android/app/
 * 4. Re-add firebase-messaging dependency to app/build.gradle
 * 5. Restore the full implementation of this class
 */
public class AlamadaMessagingService {

    /** No-op — kept so existing call sites in BaseActivity compile. */
    public static void createNotificationChannel(Context context) {
        // No-op until real Firebase is configured
    }

    private AlamadaMessagingService() {}
}
