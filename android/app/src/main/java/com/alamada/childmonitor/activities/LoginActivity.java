package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.databinding.ActivityLoginBinding;
import com.alamada.childmonitor.models.AuthResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {

    private ActivityLoginBinding binding;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityLoginBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        // Hide the default action bar — we have a custom header
        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        binding.btnLogin.setOnClickListener(v -> attemptLogin());

        // Password eye toggle
        binding.btnTogglePassword.setOnClickListener(v -> {
            android.text.method.TransformationMethod tm =
                binding.etPassword.getTransformationMethod();
            if (tm instanceof android.text.method.PasswordTransformationMethod) {
                binding.etPassword.setTransformationMethod(
                    android.text.method.HideReturnsTransformationMethod.getInstance());
                binding.btnTogglePassword.setText("🙈");
            } else {
                binding.etPassword.setTransformationMethod(
                    android.text.method.PasswordTransformationMethod.getInstance());
                binding.btnTogglePassword.setText("👁");
            }
            // Keep cursor at end
            binding.etPassword.setSelection(binding.etPassword.length());
        });

        binding.tvForgotPassword.setOnClickListener(v ->
            Toast.makeText(this,
                "Please contact the learning center administrator to reset your password.",
                Toast.LENGTH_LONG).show()
        );
    }

    private void attemptLogin() {
        String email    = binding.etEmail.getText().toString().trim();
        String password = binding.etPassword.getText().toString().trim();

        if (email.isEmpty()) {
            binding.etEmail.setError("Email address is required");
            binding.etEmail.requestFocus();
            return;
        }
        if (!email.contains("@")) {
            binding.etEmail.setError("Please enter your email address (e.g. juan@gmail.com)");
            binding.etEmail.requestFocus();
            return;
        }
        if (password.isEmpty()) {
            binding.etPassword.setError("Password is required");
            binding.etPassword.requestFocus();
            return;
        }

        setLoading(true);
        hideError();

        // After 3 s still loading — show a friendly "server waking up" hint
        new android.os.Handler(android.os.Looper.getMainLooper()).postDelayed(() -> {
            if (binding.progressBar.getVisibility() == View.VISIBLE) {
                showError("⏳ Logging in… the server may be starting up. Please wait a moment.");
            }
        }, 3_000);

        Map<String, String> body = new HashMap<>();
        body.put("email",    email);
        body.put("password", password);

        ApiClient.getInstance(null)
            .login(body)
            .enqueue(new Callback<AuthResponse>() {
                @Override
                public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                    setLoading(false);
                    if (response.isSuccessful() && response.body() != null) {
                        AuthResponse auth = response.body();

                        session.saveSession(
                            auth.token,
                            auth.user.id,
                            auth.user.name,
                            auth.user.email,
                            auth.user.role
                        );

                        // Register FCM token with the backend for push notifications
                        // (Firebase disabled — no placeholder google-services.json)
                        // registerFcmToken(auth.token);

                        // Route based on role: staff/admin → staff dashboard, parent → parent dashboard
                        Class<?> target = "parent".equals(auth.user.role)
                            ? DashboardActivity.class
                            : StaffDashboardActivity.class;

                        Intent intent = new Intent(LoginActivity.this, target);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        startActivity(intent);

                    } else if (response.code() == 422) {
                        showError("Wrong email or password. Please check and try again.");
                    } else if (response.code() == 403) {
                        showError("Your account is inactive. Contact the administrator.");
                    } else if (response.code() == 500 || response.code() == 503) {
                        showError("⏳ Server is waking up. Please wait 30 seconds and try again.");
                    } else {
                        showError("Login failed (error " + response.code() + "). Please try again.");
                    }
                }

                @Override
                public void onFailure(Call<AuthResponse> call, Throwable t) {
                    setLoading(false);
                    String msg = t.getMessage() != null ? t.getMessage() : "";
                    if (msg.toLowerCase().contains("timeout") || msg.toLowerCase().contains("connect")) {
                        showError("Could not reach the server. It may be waking up — please wait a moment and try again.");
                    } else {
                        showError("Network error: " + msg);
                    }
                }
            });
    }

    private void setLoading(boolean loading) {
        binding.btnLogin.setEnabled(!loading);
        binding.btnLogin.setText(loading ? "Logging in…" : "Log In");
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
    }

    private void showError(String message) {
        binding.tvError.setText(message);
        // Amber colour for informational "waking up" message, red for real errors
        if (message.startsWith("⏳")) {
            binding.tvError.setTextColor(
                getResources().getColor(android.R.color.holo_orange_dark, null));
            binding.tvError.setBackgroundColor(
                android.graphics.Color.parseColor("#FFF8E1"));
        } else {
            binding.tvError.setTextColor(
                getResources().getColor(android.R.color.holo_red_dark, null));
            binding.tvError.setBackgroundColor(
                android.graphics.Color.parseColor("#FFEBEE"));
        }
        binding.tvError.setVisibility(View.VISIBLE);
    }

    private void hideError() {
        binding.tvError.setVisibility(View.GONE);
    }

    /**
     * Sends the FCM registration token to the backend so the server can
     * send push notifications to this device.
     */
    /**
     * FCM disabled — requires a real google-services.json from Firebase Console.
     * Push notifications will work once a real Firebase project is configured.
     */
    private void registerFcmToken(String token) {
        // No-op until real Firebase project is set up
    }
}
