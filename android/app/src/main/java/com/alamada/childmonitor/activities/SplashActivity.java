package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;
import com.google.android.material.button.MaterialButton;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Splash screen — wakes up the Render backend before navigating.
 *
 * Free Render plan sleeps after 15 min inactivity and takes 30–60 s to wake.
 * We ping /api/health here with clear progress messages and a Retry button
 * so parents know what's happening and aren't stuck indefinitely.
 */
public class SplashActivity extends AppCompatActivity {

    private TextView tvStatus;
    private MaterialButton btnRetry;
    private boolean navigated = false;
    private int retryCount = 0;
    private Handler handler;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        tvStatus = findViewById(R.id.tv_splash_status);
        btnRetry = findViewById(R.id.btn_retry);
        handler  = new Handler(Looper.getMainLooper());

        btnRetry.setOnClickListener(v -> {
            retryCount = 0;
            btnRetry.setVisibility(View.GONE);
            pingServer();
        });

        pingServer();
    }

    // ── Server wake-up ping ────────────────────────────────────────

    private void pingServer() {
        setStatus("Connecting to server…");

        // Clear any pending callbacks from a previous attempt
        handler.removeCallbacksAndMessages(null);

        // Single ping — OkHttp will handle the timeout (90 s)
        ApiClient.getInstance(null)
            .pingHealth()
            .enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call,
                                       Response<ResponseBody> response) {
                    // Server is awake — navigate
                    navigateNext();
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    // Network error (no WiFi, timeout, etc.)
                    runOnUiThread(() -> {
                        if (navigated) return;
                        setStatus("No connection. Check your internet and retry.");
                        btnRetry.setVisibility(View.VISIBLE);
                    });
                }
            });

        // Escalating status messages every few seconds
        handler.postDelayed(() -> setStatus("Server is waking up… (this can take 30–60 s)"), 4_000);
        handler.postDelayed(() -> setStatus("Still starting, almost there…"), 18_000);
        handler.postDelayed(() -> setStatus("Taking longer than usual…"), 35_000);

        // Show Retry button after 50 s so user isn't stuck forever
        handler.postDelayed(() -> {
            if (!navigated) {
                setStatus("Server is taking a while. You can retry or wait.");
                btnRetry.setVisibility(View.VISIBLE);
            }
        }, 50_000);

        // Hard safety-net at 90 s — navigate anyway
        handler.postDelayed(this::navigateNext, 90_000);
    }

    // ── Navigation ────────────────────────────────────────────────

    private synchronized void navigateNext() {
        if (navigated) return;
        navigated = true;
        handler.removeCallbacksAndMessages(null);

        runOnUiThread(() -> {
            SessionManager session = new SessionManager(this);
            if (session.isLoggedIn()) {
                String role = session.getUserRole();
                Class<?> target = "parent".equals(role)
                    ? DashboardActivity.class
                    : StaffDashboardActivity.class;
                Intent intent = new Intent(this, target);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                    | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(intent);
            } else {
                Intent intent = new Intent(this, LoginActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                    | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(intent);
            }
            finish();
        });
    }

    // ── Helper ────────────────────────────────────────────────────

    private void setStatus(String msg) {
        runOnUiThread(() -> {
            if (tvStatus != null) tvStatus.setText(msg);
        });
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (handler != null) handler.removeCallbacksAndMessages(null);
    }
}
