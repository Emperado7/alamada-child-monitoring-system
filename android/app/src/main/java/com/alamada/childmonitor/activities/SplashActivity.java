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
 * Key UX improvements:
 *  - "Skip to Login" button shown immediately so users never feel trapped.
 *  - Retry shown at 20 s (was 50 s) with auto-retry after another 30 s.
 *  - Hard safety-net cut to 30 s (was 90 s) — go to Login even if server
 *    hasn't responded; the login screen's own timeout messaging covers the rest.
 */
public class SplashActivity extends AppCompatActivity {

    private TextView       tvStatus;
    private MaterialButton btnRetry;
    private MaterialButton btnSkip;
    private boolean        navigated  = false;
    private Call<?>        pingCall   = null;   // track in-flight call so we can cancel on skip
    private Handler        handler;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        tvStatus = findViewById(R.id.tv_splash_status);
        btnRetry = findViewById(R.id.btn_retry);
        btnSkip  = findViewById(R.id.btn_skip);
        handler  = new Handler(Looper.getMainLooper());

        // Retry — start a fresh ping attempt
        btnRetry.setOnClickListener(v -> {
            btnRetry.setVisibility(View.GONE);
            pingServer();
        });

        // Skip — go straight to Login (or dashboard if already logged in)
        btnSkip.setOnClickListener(v -> navigateNext());

        pingServer();
    }

    // ── Server wake-up ping ───────────────────────────────────────────────────

    @SuppressWarnings("unchecked")
    private void pingServer() {
        setStatus("Connecting to server…");
        btnRetry.setVisibility(View.GONE);

        // Cancel any in-flight call before starting a new one
        if (pingCall != null) pingCall.cancel();

        handler.removeCallbacksAndMessages(null);

        // Show "Skip to Login" immediately so users are never stuck
        btnSkip.setVisibility(View.VISIBLE);

        pingCall = ApiClient.getInstance(null).pingHealth();
        ((Call<ResponseBody>) pingCall).enqueue(new Callback<ResponseBody>() {
            @Override
            public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                // Server is awake — navigate
                navigateNext();
            }

            @Override
            public void onFailure(Call<ResponseBody> call, Throwable t) {
                if (call.isCanceled()) return;  // user tapped Skip
                runOnUiThread(() -> {
                    if (navigated) return;
                    setStatus("No connection — check your internet and retry.");
                    btnRetry.setVisibility(View.VISIBLE);
                });
            }
        });

        // 5 s  — first hint that the server is cold-starting
        handler.postDelayed(
            () -> setStatus("Server is waking up… (first launch can take ~30 s)"),
            5_000);

        // 12 s — mid-progress reassurance
        handler.postDelayed(
            () -> setStatus("Almost there, still starting up…"),
            12_000);

        // 20 s — show Retry (was 50 s) and schedule an auto-retry after 30 more seconds
        handler.postDelayed(() -> {
            if (navigated) return;
            setStatus("Taking longer than usual — tap Retry or Skip to go straight to Login.");
            btnRetry.setVisibility(View.VISIBLE);
        }, 20_000);

        // 30 s — hard safety-net: navigate anyway (was 90 s)
        // The login screen has its own timeout feedback, so going there early is fine.
        handler.postDelayed(this::navigateNext, 30_000);
    }

    // ── Navigation ───────────────────────────────────────────────────────────

    private synchronized void navigateNext() {
        if (navigated) return;
        navigated = true;
        if (pingCall != null) pingCall.cancel();
        handler.removeCallbacksAndMessages(null);

        runOnUiThread(() -> {
            SessionManager session = new SessionManager(this);
            Class<?> target;
            if (session.isLoggedIn()) {
                String role = session.getUserRole();
                target = "parent".equals(role)
                    ? DashboardActivity.class
                    : StaffDashboardActivity.class;
            } else {
                target = LoginActivity.class;
            }
            Intent intent = new Intent(this, target);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(intent);
            finish();
        });
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private void setStatus(String msg) {
        runOnUiThread(() -> {
            if (tvStatus != null) tvStatus.setText(msg);
        });
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (handler != null) handler.removeCallbacksAndMessages(null);
        if (pingCall != null) pingCall.cancel();
    }
}
