package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;

import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Base class for all activities.
 * Provides shared session-expiry handling and logout.
 */
public abstract class BaseActivity extends AppCompatActivity {

    protected SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        session = new SessionManager(this);

        // Create notification channel for FCM pushes (Android 8.0+)
        com.alamada.childmonitor.network.AlamadaMessagingService.createNotificationChannel(this);

        // Request POST_NOTIFICATIONS permission on Android 13+ (API 33+)
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this,
                android.Manifest.permission.POST_NOTIFICATIONS)
                != android.content.pm.PackageManager.PERMISSION_GRANTED) {
                androidx.core.app.ActivityCompat.requestPermissions(this,
                    new String[]{android.Manifest.permission.POST_NOTIFICATIONS}, 1001);
            }
        }
    }

    // ── Call this whenever an API returns 401 ─────────────────────

    protected void handleUnauthorised() {
        runOnUiThread(this::doLogout);
    }

    // ── User-initiated logout (calls API then clears session) ─────

    protected void doLogout() {
        String token = session.getBearerToken();
        // Fire-and-forget — we clear the session regardless of response
        try {
            ApiClient.getInstance(token)
                .logout()
                .enqueue(new Callback<MessageResponse>() {
                    @Override public void onResponse(Call<MessageResponse> c, Response<MessageResponse> r) {}
                    @Override public void onFailure(Call<MessageResponse> c, Throwable t) {}
                });
        } catch (Exception ignored) {}

        session.clearSession();
        ApiClient.clear();

        Intent i = new Intent(this, LoginActivity.class);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(i);
        finish();
    }
}
