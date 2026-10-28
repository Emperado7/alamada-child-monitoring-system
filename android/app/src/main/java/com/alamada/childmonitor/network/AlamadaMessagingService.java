package com.alamada.childmonitor.network;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Build;

import androidx.core.app.NotificationCompat;
import androidx.core.content.ContextCompat;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.activities.SplashActivity;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

/**
 * Receives push notifications from Firebase Cloud Messaging (FCM).
 *
 * Creates a system notification that opens the app when tapped.
 * The notification title and body come from the FCM "notification" payload
 * sent by the backend's FcmService.
 */
public class AlamadaMessagingService extends FirebaseMessagingService {

    private static final String CHANNEL_ID    = "alamada_notifications";
    private static final String CHANNEL_NAME   = "Alamada Notifications";
    private static final int    NOTIF_ID_BASE  = 1001;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        String title = "Alamada Learning Center";
        String body  = "";

        // Prefer the notification payload (sent by backend)
        if (remoteMessage.getNotification() != null) {
            title = remoteMessage.getNotification().getTitle() != null
                ? remoteMessage.getNotification().getTitle() : title;
            body  = remoteMessage.getNotification().getBody() != null
                ? remoteMessage.getNotification().getBody() : body;
        }

        // Also check data payload as fallback
        if (body.isEmpty() && remoteMessage.getData().containsKey("message")) {
            body = remoteMessage.getData().get("message");
        }
        if (remoteMessage.getData().containsKey("title")) {
            title = remoteMessage.getData().get("title");
        }

        showNotification(title, body);
    }

    /**
     * Called when a new FCM token is generated (on first install, app data cleared, etc.)
     * The token is saved to SharedPreferences and will be sent to the backend on next login.
     */
    @Override
    public void onNewToken(String token) {
        super.onNewToken(token);
        // Save the token so the login flow can register it with the backend
        getSharedPreferences("AlamadaFCM", MODE_PRIVATE)
            .edit()
            .putString("fcm_token", token)
            .apply();
    }

    private void showNotification(String title, String body) {
        createNotificationChannel(this);

        Intent intent = new Intent(this, SplashActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        PendingIntent pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT
        );

        NotificationCompat.Builder builder = new NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.mipmap.ic_launcher)
            .setContentTitle(title)
            .setContentText(body)
            .setStyle(new NotificationCompat.BigTextStyle().bigText(body))
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent);

        NotificationManager nm = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        if (nm != null) {
            nm.notify(NOTIF_ID_BASE + (int) (System.currentTimeMillis() % 1000), builder.build());
        }
    }

    static void createNotificationChannel(Context context) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            );
            channel.setDescription("Notifications from Alamada Learning Center");
            channel.enableVibration(true);

            NotificationManager nm = context.getSystemService(NotificationManager.class);
            if (nm != null) {
                nm.createNotificationChannel(channel);
            }
        }
    }
}
