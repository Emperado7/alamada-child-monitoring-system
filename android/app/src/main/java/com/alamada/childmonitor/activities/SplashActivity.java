package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Splash screen.
 *
 * On the free Render plan the backend spins down after inactivity and can take
 * 30–60 s to wake up. We ping /api/health here so the server is warm before the
 * user reaches the login screen. A status message keeps the user informed.
 */
public class SplashActivity extends AppCompatActivity {

    private TextView tvStatus;
    private boolean navigated = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        tvStatus = findViewById(R.id.tv_splash_status);

        // Kick off the health ping immediately
        pingServer();

        // Safety net: navigate after 65 s even if the ping never returns
        new Handler(Looper.getMainLooper()).postDelayed(this::navigateNext, 65_000);
    }

    // ── Server wake-up ping ────────────────────────────────────────

    private void pingServer() {
        setStatus("Connecting to server…");

        // Use a no-auth client just for the health endpoint
        ApiClient.getInstance(null)
            .pingHealth()
            .enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                    // Server is awake — navigate immediately
                    navigateNext();
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    // Network error — still navigate, Login will show a proper error
                    navigateNext();
                }
            });

        // Show escalating status messages while waiting
        new Handler(Looper.getMainLooper()).postDelayed(
            () -> setStatus("Server is waking up…"), 3_000);
        new Handler(Looper.getMainLooper()).postDelayed(
            () -> setStatus("Still starting up, please wait…"), 15_000);
        new Handler(Looper.getMainLooper()).postDelayed(
            () -> setStatus("Almost ready…"), 35_000);
    }

    // ── Navigation ────────────────────────────────────────────────

    private synchronized void navigateNext() {
        if (navigated) return;
        navigated = true;

        runOnUiThread(() -> {
            SessionManager session = new SessionManager(this);
            if (session.isLoggedIn()) {
                String role = session.getUserRole();
                Class<?> target = "parent".equals(role)
                    ? DashboardActivity.class
                    : StaffDashboardActivity.class;
                Intent intent = new Intent(this, target);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(intent);
            } else {
                Intent intent = new Intent(this, LoginActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
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
}
